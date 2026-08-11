<?php
namespace App\Services\AI;

use App\DTOs\ParsedReceipt;
use App\DTOs\ParsedReceiptItem;

class DummyReceiptParser implements ReceiptParserInterface
{
    public function parse(string $ocrText): ParsedReceipt
    {
        // Simulated AI Parsing response matching the schema
        return new ParsedReceipt(
            merchantName: "Indomaret",
            transactionDate: now()->format('Y-m-d'),
            transactionTime: now()->format('H:i:s'),
            invoiceNumber: "INV-12345",
            items: [
                new ParsedReceiptItem(
                    name: "Aqua 600ml",
                    quantity: 2,
                    unitPrice: 3000,
                    totalPrice: 6000,
                    confidence: 0.98
                ),
            ],
            subtotal: 6000,
            total: 6000,
            calculationMismatch: false
        );
    }
}
