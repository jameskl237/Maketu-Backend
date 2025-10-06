<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    protected $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Récupérer tous les médias d'un produit
     */
    public function getMediasByProductId($productId)
    {
        return $this->mediaRepository->getMediasByProductId($productId);
    }

    /**
     * Récupérer l'image principale d'un produit
     */
    public function getPrincipalMediaByProductId($productId)
    {
        return $this->mediaRepository->getPrincipalMediaByProductId($productId);
    }

    /**
     * Créer un média pour un produit
     */
    public function createMediaForProduct($productId, array $data)
    {
        $data['product_id'] = $productId;
        return $this->mediaRepository->create($data);
    }

    /**
     * Upload et créer un média à partir d'un fichier
     */
    public function uploadAndCreateMedia($productId, UploadedFile $file, $type = 'image', $isPrincipal = false)
    {
        // Validation du fichier
        $this->validateFile($file, $type);

        // Génération du nom de fichier unique
        $filename = $this->generateUniqueFilename($file, $type);
        
        // Détermination du chemin de stockage
        $storagePath = $this->getStoragePath($type);
        $fullPath = $file->storeAs($storagePath, $filename, 'public');

        // Création de l'enregistrement en base
        $mediaData = [
            'url' => $fullPath,
            'type' => $type,
            'is_principal' => $isPrincipal,
            'product_id' => $productId
        ];

        return $this->mediaRepository->create($mediaData);
    }

    /**
     * Upload multiple fichiers pour un produit
     */
    public function uploadMultipleMedias($productId, array $files, $type = 'image', $setFirstAsPrincipal = true)
    {
        $uploadedMedias = collect();
        
        foreach ($files as $index => $file) {
            $isPrincipal = $setFirstAsPrincipal && $index === 0;
            $media = $this->uploadAndCreateMedia($productId, $file, $type, $isPrincipal);
            $uploadedMedias->push($media);
        }

        return $uploadedMedias;
    }

    /**
     * Mettre à jour un média
     */
    public function updateMedia($mediaId, array $data)
    {
        return $this->mediaRepository->update($mediaId, $data);
    }

    /**
     * Définir un média comme principal
     */
    public function setAsPrincipal($mediaId, $productId)
    {
        // D'abord, retirer le statut principal de tous les médias du produit
        $this->mediaRepository->removePrincipalFromProduct($productId);
        
        // Puis définir le média sélectionné comme principal
        return $this->mediaRepository->update($mediaId, ['is_principal' => true]);
    }

    /**
     * Supprimer un média (fichier + enregistrement)
     */
    public function deleteMedia($mediaId)
    {
        $media = $this->mediaRepository->find($mediaId);
        
        if (!$media) {
            return false;
        }

        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($media->url)) {
            Storage::disk('public')->delete($media->url);
        }

        // Supprimer l'enregistrement en base
        return $this->mediaRepository->delete($mediaId);
    }

    /**
     * Supprimer tous les médias d'un produit
     */
    public function deleteAllMediasFromProduct($productId)
    {
        $medias = $this->getMediasByProductId($productId);
        
        foreach ($medias as $media) {
            $this->deleteMedia($media->id);
        }

        return true;
    }

    /**
     * Valider un fichier uploadé
     */
    private function validateFile(UploadedFile $file, $type)
    {
        $maxSize = $type === 'video' ? 50 * 1024 * 1024 : 5 * 1024 * 1024; // 50MB pour vidéo, 5MB pour image
        
        if ($file->getSize() > $maxSize) {
            throw new \Exception("Le fichier est trop volumineux. Taille maximale: " . ($maxSize / 1024 / 1024) . "MB");
        }

        $allowedMimes = $type === 'video' 
            ? ['video/mp4', 'video/avi', 'video/mov', 'video/wmv']
            : ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception("Type de fichier non autorisé pour " . $type);
        }
    }

    /**
     * Générer un nom de fichier unique
     */
    private function generateUniqueFilename(UploadedFile $file, $type)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        
        return "{$type}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Obtenir le chemin de stockage selon le type
     */
    private function getStoragePath($type)
    {
        return $type === 'video' ? 'products/videos' : 'products/images';
    }

    /**
     * Obtenir l'URL publique d'un média
     */
    public function getMediaUrl($media)
    {
        return Storage::url($media->url);
    }

    /**
     * Obtenir les URLs publiques de tous les médias d'un produit
     */
    public function getProductMediaUrls($productId)
    {
        $medias = $this->getMediasByProductId($productId);
        
        return $medias->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $this->getMediaUrl($media),
                'type' => $media->type,
                'is_principal' => $media->is_principal,
                'created_at' => $media->created_at
            ];
        });
    }
}

