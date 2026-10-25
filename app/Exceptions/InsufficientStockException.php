<?php

namespace App\Exceptions;

use App\Models\Product;
use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(public readonly Product $product)
    {
        parent::__construct("Not enough stock for product: {$product->title}");
    }
}