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
            $result = $this->authService->login($request->validated());

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
            dd($e);
            return ApiResponse::error(
                'Erreur lors de la connexion',
                HttpStatus::INTERNAL_SERVER_ERROR,
                ['exception' => $e->getMessage()]
            );
        }
    }
}
