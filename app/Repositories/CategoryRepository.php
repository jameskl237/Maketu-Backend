<?php

namespace App\Repositories;
use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function getAllCategoriesWithProducts()
    {
        return $this->model::with('products')->get();
    }

    public function getCategoryWithProducts($id)
    {
        return $this->model::with('products')->find($id);
    }
}