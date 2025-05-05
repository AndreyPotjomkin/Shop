<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Product with ID {$id} not found");
    }
} 