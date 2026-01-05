<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderService
{
    protected $orderRepository;
    protected $productRepository;

    public function __construct(OrderRepository $orderRepository, ProductRepository $productRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function getAllOrders()
    {
        return $this->orderRepository->all();
    }

    public function createOrder(array $data)
    {
        $totalPrice = 0;
        $totalProducts = 0;
        $products = [];

        foreach ($data['products'] as $productData) {
            $product = $this->productRepository->find($productData['product_id']);
            $quantity = $productData['quantity'];
            $totalPrice += $product->price * $quantity;
            $totalProducts += $quantity;

            $products[$product->id] = [  
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        $order = $this->orderRepository->create([
            'order_number' => 'ORD-' . Str::uuid(),
            'user_id' => Auth::id(),
            'total_products' => $totalProducts,
            'total_price' => $totalPrice,
        ]);

        $order->products()->attach($products);

        return $order;
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->find($id);
    }
}
