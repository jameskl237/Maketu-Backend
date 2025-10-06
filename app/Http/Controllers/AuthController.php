<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService){}
    
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            $result = $this->authService->login($validated);

            return ApiResponse::success(
                $result,
                'Connexion réussie',
                HttpStatus::OK
            );
        } catch (ValidationException $e) {
            return ApiResponse::error(
                'Identifiants invalides',
                HttpStatus::UNAUTHORIZED,
                $e->errors()
            );
        } catch (\Exception $e) {
            // dd($e);
            return ApiResponse::error(
                'Erreur lors de la connexion',
                HttpStatus::INTERNAL_SERVER_ERROR,
                ['exception' => $e->getMessage()]
            );
        }
    }

    public function getUserConnected(Request $request)
    {
        $user = $request->user()->load('shops');
        return ApiResponse::success(
            $user,
            'Utilisateur connecté récupéré',
            HttpStatus::OK
        );
    }

     public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success([], 'Déconnexion réussie', HttpStatus::OK);
    }
}
