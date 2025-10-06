<?php

namespace App\Repositories;
use App\Models\Media;

class MediaRepository extends BaseRepository
{
    public function __construct(Media $media)
    {
        parent::__construct($media);
    }

    public function getMediasByProductId($productId)
    {
        return $this->model::where('product_id', $productId)->orderBy('is_principal', 'desc')->get();
    }

    public function getPrincipalMediaByProductId($productId)
    {
        return $this->model::where('product_id', $productId)
                          ->where('is_principal', true)
                          ->first();
    }

    public function removePrincipalFromProduct($productId)
    {
        return $this->model::where('product_id', $productId)
                          ->where('is_principal', true)
                          ->update(['is_principal' => false]);
    }

    public function getMediasByType($productId, $type)
    {
        return $this->model::where('product_id', $productId)
                          ->where('type', $type)
                          ->get();
    }

    public function getImagesByProductId($productId)
    {
        return $this->getMediasByType($productId, 'image');
    }

    public function getVideosByProductId($productId)
    {
        return $this->getMediasByType($productId, 'video');
    }

    public function countMediasByProductId($productId)
    {
        return $this->model::where('product_id', $productId)->count();
    }

    public function getMediasWithUrls($productId)
    {
        return $this->model::where('product_id', $productId)
                          ->orderBy('is_principal', 'desc')
                          ->orderBy('created_at', 'asc')
                          ->get();
    }
}