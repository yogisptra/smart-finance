<?php

namespace App\Jobs;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AI\ReceiptParserInterface;
use App\Enums\ReceiptStatus;

class ParseReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(protected int $receiptId) {}

    public function handle(ReceiptParserInterface $parserService): void
    {
        $receipt = Receipt::with('ocrResult')->findOrFail($this->receiptId);

        $receipt->update(['status' => ReceiptStatus::PARSING]);

        try {
            $parsedReceipt = $parserService->parse($receipt->ocrResult->raw_text);

            $receipt->ocrResult()->update([
                'parsed_data' => json_decode(json_encode($parsedReceipt), true),
            ]);

            // Save items if exist
            if (!empty($parsedReceipt->items)) {
                foreach ($parsedReceipt->items as $item) {
                    $receipt->items()->create([
                        'name' => $item->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unitPrice,
                        'total_price' => $item->totalPrice,
                        'discount' => $item->discount,
                        'tax' => $item->tax,
                        'confidence' => $item->confidence,
                    ]);
                }
            }

            $receipt->update([
                'status' => ReceiptStatus::READY_FOR_REVIEW,
                'processed_at' => now(),
            ]);

        } catch (\Exception $e) {
            $receipt->update(['status' => ReceiptStatus::FAILED]);
            // Log error
        }
    }
}
