<?php

namespace App\Repositories;
use App\Models\Shop;

class ShopRepository extends BaseRepository
{
    public function __construct(Shop $shop)
    {
        parent::__construct($shop);
    }
}