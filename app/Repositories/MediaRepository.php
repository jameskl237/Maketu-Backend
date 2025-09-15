<?php

namespace App\Repositories;
use App\Models\Media;

class MediaRepository extends BaseRepository
{
    public function __construct($media)
    {
        parent::__construct($media);
    }

    public function getMediasByProductId($productId)
    {
        return $this->model::where('product_id', $productId)->get();
    }
}