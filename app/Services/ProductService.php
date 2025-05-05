<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ProductDTO;
use App\Events\ProductCreated;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductService implements ProductServiceInterface
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->all();
    }

    public function getProduct(int $id): Product
    {
        return Cache::remember(
            "product.{$id}",
            self::CACHE_TTL,
            function () use ($id) {
                $product = $this->productRepository->find($id);

                if (!$product) {
                    throw new ProductNotFoundException($id);
                }

                return $product;
            }
        );
    }

    public function createProduct(ProductDTO $data): Product
    {
        $product = $this->productRepository->create([
            'name' => $data->name,
            'price' => $data->price,
            'stock' => $data->stock,
        ]);

        event(new ProductCreated($product));

        return $product;
    }

    public function createProducts(array $products): Collection
    {
        $data = array_map(
            fn (ProductDTO $dto) => [
                'name' => $dto->name,
                'price' => $dto->price,
                'stock' => $dto->stock,
            ],
            $products
        );

        $createdProducts = $this->productRepository->createMany($data);

        foreach ($createdProducts as $product) {
            event(new ProductCreated($product));
        }

        return $createdProducts;
    }

    public function updateProduct(int $id, ProductDTO $data): bool
    {
        $updated = $this->productRepository->update($id, [
            'name' => $data->name,
            'price' => $data->price,
            'stock' => $data->stock,
        ]);

        if (!$updated) {
            throw new ProductNotFoundException($id);
        }

        Cache::forget("product.{$id}");

        return true;
    }

    public function deleteProduct(int $id): bool
    {
        $deleted = $this->productRepository->delete($id);

        if (!$deleted) {
            throw new ProductNotFoundException($id);
        }

        Cache::forget("product.{$id}");

        return true;
    }

    public function isInStock(int $id): bool
    {
        try {
            $product = $this->getProduct($id);
            return $product->stock > 0;
        } catch (ProductNotFoundException) {
            return false;
        }
    }

    public function increaseStock(int $id, int $amount): void
    {
        $product = $this->getProduct($id);
        $this->productRepository->incrementStock($id, $amount);
        Cache::forget("product.{$id}");
    }

    public function decreaseStock(int $id, int $amount): void
    {
        $product = $this->getProduct($id);

        if ($product->stock < $amount) {
            throw new InsufficientStockException($id, $amount);
        }

        $this->productRepository->decrementStock($id, $amount);
        Cache::forget("product.{$id}");
    }

    public function paginateProducts(int $perPage = 10): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage);
    }
} 