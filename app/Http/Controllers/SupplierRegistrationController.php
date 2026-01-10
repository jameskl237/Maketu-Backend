<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use App\Services\AuthService;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SupplierRegistrationController extends Controller
{
    protected $authService;
    protected $shopService;

    public function __construct(AuthService $authService, ShopService $shopService)
    {
        $this->authService = $authService;
        $this->shopService = $shopService;
    }

    /**
     * Register a new supplier with an associated shop
     */
    public function registerSupplier(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                // User validation rules
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string|max:500',
                'password' => 'required|string|min:8|confirmed',
                
                // Shop validation rules
                'shop_name' => 'required|string|max:255',
                'shop_description' => 'nullable|string',
                'shop_city' => 'required|string|max:100',
                'shop_district' => 'nullable|string|max:100',
                'shop_phone' => 'nullable|string|max:20',
            ]);

            // Create the user as a supplier
            $userData = [
                'name' => $validatedData['name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'] ?? null,
                'password' => $validatedData['password'],
                'role' => 'supplier', // Set the role as supplier
            ];

            // Create the user first
            $authResult = $this->authService->register($userData);
            $user = $authResult['user'];

            // Create the associated shop
            $shopData = [
                'name' => $validatedData['shop_name'],
                'description' => $validatedData['shop_description'] ?? null,
                'city' => $validatedData['shop_city'],
                'district' => $validatedData['shop_district'] ?? null,
                'phone' => $validatedData['shop_phone'] ?? $validatedData['phone'], // Use user's phone if shop phone not provided
                'user_id' => $user->id, // Associate the shop with the created user
            ];

            $shop = $this->shopService->createShop($shopData);

            // Return success response with user, token and shop information
            return ApiResponse::success([
                'user' => $user,
                'shop' => $shop,
                'token' => $authResult['token'],
            ], 'Supplier account and shop created successfully', HttpStatus::CREATED);

        } catch (ValidationException $e) {
            return ApiResponse::error(
                'Validation failed',
                HttpStatus::UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error creating supplier account and shop',
                HttpStatus::INTERNAL_SERVER_ERROR,
                ['exception' => $e->getMessage()]
            );
        }
    }
}
