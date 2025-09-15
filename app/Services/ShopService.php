<?php

namespace App\Services;

use App\Models\Shop;
use App\Repositories\ShopRepository;

class ShopService 
{
    protected $shopRepository;
    
    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function getAllShops()
    {
       return $this->shopRepository->all();
    }

    public function getShopById($id)
    {
        return $this->shopRepository->find($id)->with('products')
                                                ->with('user')
                                                ->first();       
    }

    public function createShop(array $data)
    {
        return  $this->shopRepository->create($data);
         
    }

    public function updateShop($id, array $data)
    {
        return $this->shopRepository->update($id, $data);
    }

    public function deleteShop($id)
    {
        return $this->shopRepository->delete($id)->with('products')->first();
    }
}