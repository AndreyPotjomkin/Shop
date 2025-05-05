<?php

declare(strict_types=1);

namespace App\DTO;

use Spatie\LaravelData\Data;

class ProductDTO extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly int $stock,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            stock: (int) $data['stock'],
        );
    }
} 