<?php
namespace App\Services\AI;

use App\DTOs\ParsedReceipt;

interface ReceiptParserInterface
{
    public function parse(string $ocrText): ParsedReceipt;
}
