<?php
namespace App\Services\OCR;

use App\DTOs\OCRResult;

interface OCRServiceInterface
{
    public function extractText(string $filePath): OCRResult;
}
