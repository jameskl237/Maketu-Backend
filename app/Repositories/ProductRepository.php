<?php

namespace App\Repositories;
use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function getAllProductsWithCategories()
    {
        return $this->model::with('category')
                    ->with('medias')
                    ->get()
                    ->map(function ($product){
                        $category = $product->category;
                        
                        return [
                            'code' => $product->code,
                            'name' => $product->name,
                            'description' => $product->description,
                            'price' => $product->price,
                            'long_description' => $product->long_description,
                            'promotion_price'=> $product->promotion_price,
                            'in_stock' => $product->in_stock,
                            'quantity'=> $product->quantity,
                            'origin'=> $product->origin,
                            'category_name' => $category ? $category->name : null,
                            'category_description' => $category ? $category->description : null,
                        ];
                    });
    }

    public function getProductWithCategory($id)
    {
        return $this->model::with('category')->find($id);
    }

    public function getProductWithMedias($id)
    {
        return $this->model::with('medias')->find($id);
    }
}