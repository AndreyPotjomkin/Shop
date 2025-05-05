<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTO\ProductDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getAllProducts(): Collection;

    public function getProduct(int $id): Product;

    public function createProduct(ProductDTO $data): Product;

    public function createProducts(array $products): Collection;

    public function updateProduct(int $id, ProductDTO $data): bool;

    public function deleteProduct(int $id): bool;

    public function isInStock(int $id): bool;

    public function increaseStock(int $id, int $amount): void;

    public function decreaseStock(int $id, int $amount): void;

    public function paginateProducts(int $perPage = 10): LengthAwarePaginator;
} 