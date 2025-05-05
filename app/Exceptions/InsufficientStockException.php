<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(int $id, int $amount)
    {
        parent::__construct("Insufficient stock for product {$id}. Requested amount: {$amount}");
    }
} 