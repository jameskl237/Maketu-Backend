<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
    public function index(Request $request)
    {
        dd($request->fullUrl());
        try {
            $products = $this->productService->getProductsWithMedias();
            
            if ($products->isEmpty()) {
                return ApiResponse::noContent('Aucun produit trouvé');
            }

            return ApiResponse::success(
                $products,
                'Produits récupérés avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération des produits: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show($id)
    {
        try {
            $product = $this->productService->getProductWithMediaUrls($id);
            
            if (!$product) {
                return ApiResponse::notFound('Produit non trouvé');
            }

            return ApiResponse::success(
                $product,
                'Produit récupéré avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération du produit: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    public function create(Request $request)
{
    try {
        // ✅ Validation complète (correspondance parfaite avec le frontend)
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'long_description' => 'nullable|string|max:5000',
            'price'            => 'required|numeric|min:0',
            'promotion_price'  => 'nullable|numeric|min:0|lt:price',
            'in_stock'         => 'required|in:true,false,1,0',
            'quantity'         => 'required|numeric|min:0',
            'origin'           => 'required|in:local,imported',
            'category_id'      => 'required|exists:categories,id',
            'shop_id'          => 'required|exists:shops,id',
            'user_id'          => 'required|exists:users,id',

            // ✅ Le frontend envoie "files[]" (et non images/videos séparés)
            'files'            => 'required|array|max:10',
            'files.*'          => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,webm,ogg|max:10240', // 10MB max par fichier
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                'Données de validation invalides',
                HttpStatus::BAD_REQUEST,
                $validator->errors()
            );
        }

        $data = $request->only([
            'name', 'description', 'long_description', 'price',
            'promotion_price', 'in_stock', 'quantity', 'origin',
            'category_id', 'shop_id', 'user_id'
        ]);

        // ✅ Conversion explicite de 'true' / 'false' -> 1 / 0
        $data['in_stock'] = filter_var($data['in_stock'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        // ✅ Génération d’un code unique pour le produit
        $data['code'] = $this->generateUniqueProductCode();

        // ✅ Récupérer les fichiers envoyés
        $files = $request->file('files', []);

        // ✅ Séparer les fichiers en images / vidéos selon leur type MIME
        $images = [];
        $videos = [];

        foreach ($files as $file) {
            if (str_starts_with($file->getMimeType(), 'image/')) {
                $images[] = $file;
            } elseif (str_starts_with($file->getMimeType(), 'video/')) {
                $videos[] = $file;
            }
        }

        // ✅ Création du produit + upload des médias
        $product = $this->productService->createProductWithMedias($data, $images, $videos);

        return ApiResponse::success(
            $product,
            'Produit créé avec succès',
            HttpStatus::CREATED
        );

    } catch (\Exception $e) {
        return ApiResponse::error(
            'Erreur lors de la création du produit: ' . $e->getMessage(),
            HttpStatus::INTERNAL_SERVER_ERROR
        );
    }
}

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'             => 'sometimes|string|max:255',
                'description'      => 'nullable|string|max:1000',
                'long_description' => 'nullable|string|max:5000',
                'price'            => 'sometimes|numeric|min:0',
                'promotion_price'  => 'nullable|numeric|min:0|lt:price',
                'in_stock'         => 'sometimes|in:true,false,1,0',
                'quantity'         => 'sometimes|numeric|min:0',
                'origin'           => 'sometimes|in:local,imported',
                'category_id'      => 'sometimes|exists:categories,id',
                'shop_id'          => 'sometimes|exists:shops,id',

                // Gestion des médias
                'media_to_delete'   => 'nullable|array',
                'media_to_delete.*' => 'integer|exists:medias,id', // Valide que chaque ID existe
                
                'files'             => 'nullable|array|max:10',
                'files.*'           => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,webm,ogg|max:10240',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    'Données de validation invalides',
                    HttpStatus::BAD_REQUEST,
                    $validator->errors()
                );
            }

            $data = $request->only([
                'name', 'description', 'long_description', 'price', 
                'promotion_price', 'in_stock', 'quantity', 'origin', 
                'category_id', 'shop_id'
            ]);

            if ($request->has('in_stock')) {
                $data['in_stock'] = filter_var($request->input('in_stock'), FILTER_VALIDATE_BOOLEAN);
            }

            // Récupération des médias à supprimer et des nouveaux fichiers
            $mediaToDelete = $request->input('media_to_delete', []);
            $newFiles = $request->file('files', []);

            $newImages = [];
            $newVideos = [];
            foreach ($newFiles as $file) {
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $newImages[] = $file;
                } elseif (str_starts_with($file->getMimeType(), 'video/')) {
                    $newVideos[] = $file;
                }
            }

            // Mise à jour du produit avec gestion des médias
            $product = $this->productService->updateProductWithMedias($id, $data, $newImages, $newVideos, $mediaToDelete);

            if (!$product) {
                return ApiResponse::notFound('Produit non trouvé');
            }

            return ApiResponse::success(
                $product,
                'Produit mis à jour avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la mise à jour du produit: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    public function delete($id)
    {
        try {
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                return ApiResponse::notFound('Produit non trouvé');
            }

            return ApiResponse::success(
                null,
                'Produit supprimé avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la suppression du produit: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    private function generateUniqueProductCode()
    {
        do {
            $code = Str::upper(Str::random(10)); // Par exemple : "A9B8C7D6E1"
        } while (Product::where('code', $code)->exists());

        return $code;
    }
}
