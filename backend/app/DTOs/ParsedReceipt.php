<?php
namespace App\DTOs;

final class ParsedReceipt
{
    /**
     * @param array<ParsedReceiptItem> $items
     */
    public function __construct(
        public readonly ?string $merchantName = null,
        public readonly ?string $transactionDate = null,
        public readonly ?string $transactionTime = null,
        public readonly ?string $invoiceNumber = null,
        public readonly array $items = [],
        public readonly ?float $subtotal = null,
        public readonly ?float $discount = null,
        public readonly ?float $tax = null,
        public readonly ?float $serviceCharge = null,
        public readonly ?float $total = null,
        public readonly ?string $paymentMethod = null,
        public readonly bool $calculationMismatch = false,
    ) {}
}
