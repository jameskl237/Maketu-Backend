<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->getAll();
        return ApiResponse::success($users, 'Liste des utilisateurs récupérée avec succès.');
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getById($id);
            if (!$user) {
                return ApiResponse::notFound('Utilisateur non trouvé.');
            }
            return ApiResponse::success($user, 'Utilisateur récupéré avec succès.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Utilisateur non trouvé.');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue lors de la récupération de l\'utilisateur.', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Assuming registerUser handles password hashing and creation
            // However, for generic CRUD, we should use the create method from BaseService
            // and handle password hashing here or in the service's create method
            // For now, let's use the create method from the service, and ensure
            // the service or model handles password hashing for the 'password' field.

            $user = $this->userService->create($validatedData);

            return ApiResponse::success($user, 'Utilisateur créé avec succès.', HttpStatus::CREATED);
        } catch (ValidationException $e) {
            return ApiResponse::error('Erreurs de validation.', HttpStatus::UNPROCESSABLE_ENTITY, $e->errors());
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue lors de la création de l\'utilisateur.', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
            ]);

            if (empty($validatedData)) {
                return ApiResponse::error('Aucune donnée à mettre à jour fournie.', HttpStatus::BAD_REQUEST);
            }

            $user = $this->userService->update($id, $validatedData);

            if (!$user) {
                return ApiResponse::notFound('Utilisateur non trouvé.');
            }

            return ApiResponse::success($user, 'Utilisateur mis à jour avec succès.');
        } catch (ValidationException $e) {
            return ApiResponse::error('Erreurs de validation.', HttpStatus::UNPROCESSABLE_ENTITY, $e->errors());
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Utilisateur non trouvé.');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue lors de la mise à jour de l\'utilisateur.', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->userService->delete($id);

            if (!$deleted) {
                return ApiResponse::notFound('Utilisateur non trouvé ou déjà supprimé.');
            }

            return ApiResponse::noContent('Utilisateur supprimé avec succès.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Utilisateur non trouvé.');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue lors de la suppression de l\'utilisateur.', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
}
