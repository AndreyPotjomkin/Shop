<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Product;

    public function create(array $data): Product;

    public function createMany(array $data): Collection;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function incrementStock(int $id, int $amount): bool;

    public function decrementStock(int $id, int $amount): bool;

    public function paginate(int $perPage = 10): LengthAwarePaginator;
} 