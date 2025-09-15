<?php

namespace App\Http\Controllers;

use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index()
    {
        $shops = $this->shopService->getAllShops();
        return response()->json($shops, 200);
        // return view('shops.index', compact('shops'));
    }

    public function show($id)
    {
        try {
            $shop = $this->shopService->getShopById($id);

            if (!$shop) {
                return response()->json([
                    'error' => 'Shop not found'
                ], 404);
            }

            return response()->json($shop);
            // return view('shops.show', compact('shop'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the shop',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $shop = $this->shopService->createShop($data);
        return response()->json($shop, 201);
        // return redirect()->route('shops.index')->with('success', 'Shop created successfully');
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        $shop = $this->shopService->updateShop($id, $data);
        return response()->json($shop, 200);
        // return redirect()->route('shops.index')->with('success', 'Shop updated successfully');
    }

    public function delete($id)
    {
        $result = $this->shopService->deleteShop($id);
        if ($result) {
            return response()->json(['message' => 'Shop deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Shop not found or could not be deleted'], 404);
        }
        // return redirect()->route('shops.index')->with('success', 'Shop deleted successfully');
    }
}
