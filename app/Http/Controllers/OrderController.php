<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Helpers\HttpStatus;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return ApiResponse::success($orders, 'Orders retrieved successfully.');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'products' => 'required|array',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $order = $this->orderService->createOrder($request->all());

            return ApiResponse::success($order, 'Order created successfully.', HttpStatus::CREATED);
        } catch (ValidationException $e) {
            return ApiResponse::error($e->errors(), 'Validation failed.', HttpStatus::UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'An error occurred while creating the order.', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);
        if (!$order) {
            return ApiResponse::error(null, 'Order not found.', HttpStatus::NOT_FOUND);
        }
        return ApiResponse::success($order, 'Order retrieved successfully.');
    }
}
