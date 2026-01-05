<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = [], string $message = 'Succès', int $status = HttpStatus::OK, array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $status,
            'meta' => $meta,
        ], $status);
    }

    public static function error(string $message = 'Erreur', int $status = HttpStatus::BAD_REQUEST, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status' => $status,
        ], $status);
    }

    public static function noContent(string $message = 'Aucun contenu'): JsonResponse
    {
        return response()->json(null, HttpStatus::NO_CONTENT);
    }

    public static function notFound(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'status' => HttpStatus::NOT_FOUND,
        ], HttpStatus::NOT_FOUND);
    }

    public static function unauthorized(string $message = 'Non autorisé'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'status' => HttpStatus::UNAUTHORIZED,
        ], HttpStatus::UNAUTHORIZED);
    }
}