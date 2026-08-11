<?php
namespace App\DTOs;

final class ParsedReceiptItem
{
    public function __construct(
        public readonly string $name,
        public readonly int|float $quantity,
        public readonly float $unitPrice,
        public readonly float $totalPrice,
        public readonly ?float $discount = null,
        public readonly ?float $tax = null,
        public readonly ?float $confidence = null,
    ) {}
}
