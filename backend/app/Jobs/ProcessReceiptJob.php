<?php

namespace App\Jobs;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\ReceiptStatus;
use Illuminate\Support\Facades\Storage;

class ProcessReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(protected int $receiptId) {}

    public function handle(): void
    {
        $receipt = Receipt::findOrFail($this->receiptId);
        
        if ($receipt->status !== ReceiptStatus::UPLOADED) {
            return;
        }

        try {
            if (!Storage::disk('local')->exists($receipt->file_path)) {
                throw new \Exception('Receipt file not found.');
            }

            $receipt->update(['status' => ReceiptStatus::PROCESSING]);

            PerformOCRJob::dispatch($this->receiptId);

        } catch (\Exception $e) {
            $receipt->update(['status' => ReceiptStatus::FAILED]);
            // Log error...
        }
    }
}
