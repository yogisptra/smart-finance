<?php

namespace App\Jobs;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\OCR\OCRServiceInterface;
use App\Enums\ReceiptStatus;

class PerformOCRJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(protected int $receiptId) {}

    public function handle(OCRServiceInterface $ocrService): void
    {
        $receipt = Receipt::findOrFail($this->receiptId);

        try {
            $ocrResult = $ocrService->extractText(storage_path('app/' . $receipt->file_path));

            $receipt->ocrResult()->create([
                'raw_text' => $ocrResult->text,
                'confidence_score' => $ocrResult->confidence,
                'provider_request_id' => $ocrResult->providerRequestId,
            ]);

            $receipt->update(['status' => ReceiptStatus::OCR_COMPLETED]);

            ParseReceiptJob::dispatch($this->receiptId);

        } catch (\Exception $e) {
            $receipt->update(['status' => ReceiptStatus::FAILED]);
            // Log error
        }
    }
}
