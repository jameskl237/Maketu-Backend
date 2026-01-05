<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an authenticated user for testing protected routes
        $this->user = User::factory()->create([
            'password' => bcrypt('password123'), // Ensure password is hashed
        ]);
        $this->token = $this->user->createToken('test_token')->plainTextToken;
    }

    /** @test */
    public function unauthenticated_user_cannot_access_user_routes()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401); // Unauthorized

        $response = $this->postJson('/api/users', []);
        $response->assertStatus(401);

        $response = $this->putJson('/api/users/1', []);
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/users/1');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_list_all_users()
    {
        User::factory()->count(3)->create(); // Create additional users
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => ['id', 'name', 'email'] // Check for basic user structure
                     ]
                 ])
                 ->assertJsonCount(4, 'data'); // 1 created in setUp + 3 new ones
    }

    /** @test */
    public function authenticated_user_can_view_a_specific_user()
    {
        $userToView = User::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->getJson('/api/users/' . $userToView->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $userToView->email]);
    }

    /** @test */
    public function authenticated_user_cannot_view_non_existent_user()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->getJson('/api/users/999'); // Assuming 999 does not exist

        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Utilisateur non trouvé.']);
    }

    /** @test */
    public function authenticated_user_can_create_a_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'newpassword123',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->postJson('/api/users', $userData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['email' => $userData['email']]);

        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    /** @test */
    public function authenticated_user_cannot_create_user_with_invalid_data()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->postJson('/api/users', [
                             'name' => '', // Invalid name
                             'email' => 'invalid-email', // Invalid email
                             'password' => 'short', // Too short password
                         ]);

        $response->assertStatus(422) // Unprocessable Entity
                 ->assertJsonStructure(['errors' => ['name', 'email', 'password']]);
    }

    /** @test */
    public function authenticated_user_can_update_a_user()
    {
        $userToUpdate = User::factory()->create();
        $updatedData = [
            'name' => 'Updated Name',
            'email' => $this->faker->unique()->safeEmail,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->putJson('/api/users/' . $userToUpdate->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Name'])
                 ->assertJsonFragment(['email' => $updatedData['email']]);

        $this->assertDatabaseHas('users', ['id' => $userToUpdate->id, 'name' => 'Updated Name', 'email' => $updatedData['email']]);
    }

    /** @test */
    public function authenticated_user_cannot_update_non_existent_user()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->putJson('/api/users/999', ['name' => 'Non Existent']);

        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Utilisateur non trouvé.']);
    }

    /** @test */
    public function authenticated_user_can_delete_a_user()
    {
        $userToDelete = User::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->deleteJson('/api/users/' . $userToDelete->id);

        $response->assertStatus(204); // No Content

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    /** @test */
    public function authenticated_user_cannot_delete_non_existent_user()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->deleteJson('/api/users/999');

        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Utilisateur non trouvé ou déjà supprimé.']);
    }
}
