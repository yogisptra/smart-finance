<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OCR\OCRServiceInterface;
use App\Services\OCR\DummyOCRService;
use App\Services\AI\ReceiptParserInterface;
use App\Services\AI\DummyReceiptParser;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OCRServiceInterface::class, DummyOCRService::class);
        $this->app->bind(ReceiptParserInterface::class, DummyReceiptParser::class);
        $this->app->bind(\App\Services\Storage\ReceiptStorageInterface::class, \App\Services\Storage\LocalReceiptStorage::class);
    }

    public function boot(): void
    {
        //
    }
}
