<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Récupérer tous les médias d'un produit
     */
    public function getProductMedias($productId): JsonResponse
    {
        try {
            $medias = $this->mediaService->getProductMediaUrls($productId);
            
            if ($medias->isEmpty()) {
                return ApiResponse::noContent('Aucun média trouvé pour ce produit');
            }

            return ApiResponse::success(
                $medias,
                'Médias récupérés avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération des médias: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Récupérer l'image principale d'un produit
     */
    public function getPrincipalMedia($productId): JsonResponse
    {
        try {
            $media = $this->mediaService->getPrincipalMediaByProductId($productId);
            
            if (!$media) {
                return ApiResponse::noContent('Aucune image principale trouvée pour ce produit');
            }

            $mediaData = [
                'id' => $media->id,
                'url' => $this->mediaService->getMediaUrl($media),
                'type' => $media->type,
                'is_principal' => $media->is_principal,
                'created_at' => $media->created_at
            ];

            return ApiResponse::success(
                $mediaData,
                'Image principale récupérée avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération de l\'image principale: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Upload d'un seul média pour un produit
     */
    public function uploadMedia(Request $request, $productId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:51200', // 50MB max
                'type' => 'required|in:image,video',
                'is_principal' => 'boolean'
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    'Données de validation invalides',
                    HttpStatus::BAD_REQUEST,
                    $validator->errors()
                );
            }

            $file = $request->file('file');
            $type = $request->input('type');
            $isPrincipal = $request->boolean('is_principal', false);

            $media = $this->mediaService->uploadAndCreateMedia($productId, $file, $type, $isPrincipal);

            $mediaData = [
                'id' => $media->id,
                'url' => $this->mediaService->getMediaUrl($media),
                'type' => $media->type,
                'is_principal' => $media->is_principal,
                'created_at' => $media->created_at
            ];

            return ApiResponse::success(
                $mediaData,
                'Média uploadé avec succès',
                HttpStatus::CREATED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de l\'upload du média: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Upload de plusieurs médias pour un produit
     */
    public function uploadMultipleMedias(Request $request, $productId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'files' => 'required|array|min:1|max:10',
                'files.*' => 'required|file|max:51200',
                'type' => 'required|in:image,video',
                'set_first_as_principal' => 'boolean'
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    'Données de validation invalides',
                    HttpStatus::BAD_REQUEST,
                    $validator->errors()
                );
            }

            $files = $request->file('files');
            $type = $request->input('type');
            $setFirstAsPrincipal = $request->boolean('set_first_as_principal', true);

            $medias = $this->mediaService->uploadMultipleMedias($productId, $files, $type, $setFirstAsPrincipal);

            $mediasData = $medias->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $this->mediaService->getMediaUrl($media),
                    'type' => $media->type,
                    'is_principal' => $media->is_principal,
                    'created_at' => $media->created_at
                ];
            });

            return ApiResponse::success(
                $mediasData,
                'Médias uploadés avec succès',
                HttpStatus::CREATED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de l\'upload des médias: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Définir un média comme principal
     */
    public function setAsPrincipal($productId, $mediaId): JsonResponse
    {
        try {
            $media = $this->mediaService->setAsPrincipal($mediaId, $productId);

            if (!$media) {
                return ApiResponse::notFound('Média non trouvé');
            }

            $mediaData = [
                'id' => $media->id,
                'url' => $this->mediaService->getMediaUrl($media),
                'type' => $media->type,
                'is_principal' => $media->is_principal,
                'created_at' => $media->created_at
            ];

            return ApiResponse::success(
                $mediaData,
                'Média défini comme principal avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la définition du média principal: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Supprimer un média
     */
    public function deleteMedia($mediaId): JsonResponse
    {
        try {
            $deleted = $this->mediaService->deleteMedia($mediaId);

            if (!$deleted) {
                return ApiResponse::notFound('Média non trouvé');
            }

            return ApiResponse::success(
                null,
                'Média supprimé avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la suppression du média: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Supprimer tous les médias d'un produit
     */
    public function deleteAllProductMedias($productId): JsonResponse
    {
        try {
            $deleted = $this->mediaService->deleteAllMediasFromProduct($productId);

            return ApiResponse::success(
                null,
                'Tous les médias du produit ont été supprimés avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la suppression des médias: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Récupérer les statistiques des médias d'un produit
     */
    public function getMediaStats($productId): JsonResponse
    {
        try {
            $allMedias = $this->mediaService->getMediasByProductId($productId);
            $images = $allMedias->where('type', 'image');
            $videos = $allMedias->where('type', 'video');
            $principal = $allMedias->where('is_principal', true)->first();

            $stats = [
                'total_medias' => $allMedias->count(),
                'total_images' => $images->count(),
                'total_videos' => $videos->count(),
                'has_principal' => $principal ? true : false,
                'principal_media' => $principal ? [
                    'id' => $principal->id,
                    'url' => $this->mediaService->getMediaUrl($principal),
                    'type' => $principal->type
                ] : null
            ];

            return ApiResponse::success(
                $stats,
                'Statistiques des médias récupérées avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération des statistiques: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }
}

