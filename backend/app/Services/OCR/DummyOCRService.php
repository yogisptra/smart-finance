<?php
namespace App\Services\OCR;

use App\DTOs\OCRResult;

class DummyOCRService implements OCRServiceInterface
{
    public function extractText(string $filePath): OCRResult
    {
        // Simulated OCR API response
        return new OCRResult(
            text: "Dummy OCR Text for Receipt\nMerchant: Indomaret\nTotal: 31080",
            confidence: 0.95,
            providerRequestId: uniqid('ocr_')
        );
    }
}
