<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Récupérer toutes les catégories
     */
    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAllCategories();
            
            if ($categories->isEmpty()) {
                return ApiResponse::noContent('Aucune catégorie trouvée');
            }

            return ApiResponse::success(
                $categories,
                'Catégories récupérées avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération des catégories: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Récupérer une catégorie par son ID
     */
    public function show($id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            
            if (!$category) {
                return ApiResponse::notFound('Catégorie non trouvée');
            }

            return ApiResponse::success(
                $category,
                'Catégorie récupérée avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération de la catégorie: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Créer une nouvelle catégorie
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name',
                'slug' => 'nullable|string|max:255|unique:categories,slug',
                'image' => 'nullable|string|max:500',
                'description' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    'Données de validation invalides',
                    HttpStatus::BAD_REQUEST,
                    $validator->errors()
                );
            }

            $data = $request->only(['name', 'slug', 'image', 'description']);
            
            // Générer un slug automatiquement si non fourni
            if (empty($data['slug'])) {
                $data['slug'] = \Str::slug($data['name']);
            }

            $category = $this->categoryService->createCategory($data);

            return ApiResponse::success(
                $category,
                'Catégorie créée avec succès',
                HttpStatus::CREATED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la création de la catégorie: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
                'image' => 'nullable|string|max:500',
                'description' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    'Données de validation invalides',
                    HttpStatus::BAD_REQUEST,
                    $validator->errors()
                );
            }

            $data = $request->only(['name', 'slug', 'image', 'description']);
            
            // Générer un slug automatiquement si non fourni et que le nom est modifié
            if (isset($data['name']) && empty($data['slug'])) {
                $data['slug'] = \Str::slug($data['name']);
            }

            $category = $this->categoryService->updateCategory($id, $data);

            if (!$category) {
                return ApiResponse::notFound('Catégorie non trouvée');
            }

            return ApiResponse::success(
                $category,
                'Catégorie mise à jour avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            
            if (!$category) {
                return ApiResponse::notFound('Catégorie non trouvée');
            }

            // Vérifier si la catégorie a des produits associés
            if ($category->products()->count() > 0) {
                return ApiResponse::error(
                    'Impossible de supprimer cette catégorie car elle contient des produits',
                    HttpStatus::CONFLICT
                );
            }

            $deleted = $this->categoryService->deleteCategory($id);

            if (!$deleted) {
                return ApiResponse::error(
                    'Erreur lors de la suppression de la catégorie',
                    HttpStatus::INTERNAL_SERVER_ERROR
                );
            }

            return ApiResponse::success(
                null,
                'Catégorie supprimée avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la suppression de la catégorie: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Récupérer les produits d'une catégorie
     */
    public function products($id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            
            if (!$category) {
                return ApiResponse::notFound('Catégorie non trouvée');
            }

            $products = $category->products;

            return ApiResponse::success(
                [
                    'category' => $category,
                    'products' => $products
                ],
                'Produits de la catégorie récupérés avec succès',
                HttpStatus::OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erreur lors de la récupération des produits: ' . $e->getMessage(),
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }
    }
}

