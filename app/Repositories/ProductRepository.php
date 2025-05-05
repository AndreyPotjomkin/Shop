<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $model
    ) {
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function createMany(array $data): Collection
    {
        $products = collect();

        foreach ($data as $item) {
            $products->push($this->create($item));
        }

        return $products;
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function incrementStock(int $id, int $amount): bool
    {
        return $this->model->where('id', $id)->increment('stock', $amount);
    }

    public function decrementStock(int $id, int $amount): bool
    {
        return $this->model->where('id', $id)->decrement('stock', $amount);
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }
} 