<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\ProductDTO;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        
        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $productDTO = ProductDTO::fromArray($validated);
        $product = $this->productService->createProduct($productDTO);

        return response()->json($product, 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if ($product === null) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $productDTO = ProductDTO::fromArray($validated);
        $updated = $this->productService->updateProduct($id, $productDTO);

        if (!$updated) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['message' => 'Product updated successfully']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);

        if (!$deleted) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }
} 