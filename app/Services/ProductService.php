<?php

namespace App\Services;
use App\Repositories\ProductRepository;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected $productRepository;
    protected $mediaService;

    public function __construct(ProductRepository $productRepository, MediaService $mediaService)
    {
        $this->productRepository = $productRepository;
        $this->mediaService = $mediaService;
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductById($id)

    {
        return $this->productRepository->findWithRelations($id, ['category', 'medias', 'shop']);
    }

    public function createProduct(array $data)
    {
        return $this->productRepository->create($data);
        
    }

    public function createProductWithMedias(array $productData, array $images = [], array $videos = [])
    {
        // Créer le produit
        $product = $this->createProduct($productData);
        
        // Upload des images si fournies
        if (!empty($images)) {
            $this->mediaService->uploadMultipleMedias($product->id, $images, 'image', true);
        }
        
        // Upload des vidéos si fournies
        if (!empty($videos)) {
            $this->mediaService->uploadMultipleMedias($product->id, $videos, 'video', false);
        }
        
        // Recharger le produit avec ses médias
        return $this->getProductById($product->id);
    }

    public function updateProduct($id, array $data)
    {
        return $this->productRepository->update($id, $data);
    }

    public function updateProductWithMedias($id, array $productData, array $newImages = [], array $newVideos = [], array $mediaToDelete = [])
    {
        // Supprimer les médias marqués pour suppression
        if (!empty($mediaToDelete)) {
            foreach ($mediaToDelete as $mediaId) {
                // On s'assure que le média appartient bien au produit pour la sécurité
                $media = $this->mediaService->getMediaById($mediaId);
                if ($media && $media->product_id == $id) {
                    $this->mediaService->deleteMedia($mediaId);
                }
            }
        }

        // Mettre à jour les données du produit
        $product = $this->updateProduct($id, $productData);
        
        if (!$product) {
            return null;
        }
        
        // Ajouter les nouvelles images
        if (!empty($newImages)) {
            // Déterminer si un média principal doit être défini
            $hasPrincipalImage = $product->medias()->where('type', 'image')->where('is_principal', true)->exists();
            $this->mediaService->uploadMultipleMedias($product->id, $newImages, 'image', !$hasPrincipalImage);
        }
        
        // Ajouter les nouvelles vidéos
        if (!empty($newVideos)) {
            $this->mediaService->uploadMultipleMedias($product->id, $newVideos, 'video', false);
        }
        
        // Recharger le produit avec ses médias pour retourner un état à jour
        return $this->getProductById($product->id);
    }

    public function deleteProduct($id)
    {
        $product = $this->getProductById($id);
        
        if (!$product) {
            return false;
        }
        
        // Supprimer tous les médias du produit
        $this->mediaService->deleteAllMediasFromProduct($id);
        
        // Supprimer le produit
        return $this->productRepository->delete($id);
    }

    public function getProductsWithMedias()
    {
        return $this->productRepository->allWithRelations(['category', 'medias', 'shop']);
    }

    public function getProductWithMediaUrls($id)
    {
        $product = $this->getProductById($id);
        
        if (!$product) {
            return null;
        }
        
        // Ajouter les URLs publiques des médias
        $product->media_urls = $this->mediaService->getProductMediaUrls($id);
        
        return $product;
    }
}