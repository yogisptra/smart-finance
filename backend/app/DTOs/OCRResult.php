<?php
namespace App\DTOs;

final class OCRResult
{
    public function __construct(
        public readonly string $text,
        public readonly ?float $confidence = null,
        public readonly ?string $providerRequestId = null,
    ) {}
}
