<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
    public function index()
    {
        $products = $this->productService->getAllProducts();
        return response()->json($products);
        // return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product);
        // return view('products.show', compact('product'));
    }

    public function create(Request $request)
{
    $data = $request->all();

    // Génération d'un code unique
    $data['code'] = $this->generateUniqueProductCode();

    // Création du produit avec le code généré
    $product = $this->productService->createProduct($data);

    // Gestion des images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->medias()->create([
                'url' => $path,
                'type' => 'image',
                'is_principal' => false,
            ]);
        }
    }

    // Gestion des vidéos
    if ($request->hasFile('videos')) {
        foreach ($request->file('videos') as $video) {
            $path = $video->store('products/videos', 'public');
            $product->medias()->create([
                'url' => $path,
                'type' => 'video',
                'is_principal' => false,
            ]);
        }
    }

    return response()->json($product->load('medias'), 201);
        // return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    private function generateUniqueProductCode()
{
    do {
        $code = Str::upper(Str::random(10)); // Par exemple : "A9B8C7D6E1"
    } while (Product::where('code', $code)->exists());

    return $code;
}
}
