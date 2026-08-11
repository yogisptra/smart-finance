<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\AuditLog;
use App\Http\Requests\StoreReceiptRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessReceiptJob;
use App\Enums\ReceiptStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Services\Storage\ReceiptStorageInterface;
use Illuminate\Support\Facades\Cache;

class ReceiptController extends Controller
{
    public function __construct(protected ReceiptStorageInterface $storage) {}

    public function store(StoreReceiptRequest $request)
    {
        $file = $request->file('image');
        $user = $request->user();
        
        $year = now()->format('Y');
        $month = now()->format('m');
        $filename = 'receipt_' . (string) str()->ulid() . '.' . $file->getClientOriginalExtension();
        $basePath = "receipts/{$user->id}/{$year}/{$month}";
        $path = $this->storage->store($file, $basePath, $filename);

        $receipt = DB::transaction(function () use ($user, $file, $path, $filename, $request) {
            $receipt = $user->receipts()->create([
                'file_name' => $filename,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => ReceiptStatus::UPLOADED,
                'uploaded_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'UPLOAD_RECEIPT',
                'entity_type' => 'Receipt',
                'entity_id' => $receipt->id,
                'new_data' => $receipt->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $receipt;
        });

        ProcessReceiptJob::dispatch($receipt->id);

        return response()->json([
            'success' => true,
            'message' => 'Receipt uploaded and is being processed',
            'data' => $receipt
        ], 201);
    }

    public function show(Request $request, Receipt $receipt)
    {
        if ($receipt->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipt retrieved successfully',
            'data' => $receipt->load(['ocrResult', 'items', 'transaction'])
        ]);
    }

    public function status(Request $request, Receipt $receipt)
    {
        if ($receipt->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipt status retrieved',
            'data' => ['status' => $receipt->status]
        ]);
    }

    public function image(Request $request, Receipt $receipt)
    {
        if ($receipt->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$this->storage->exists($receipt->file_path)) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        return response()->file($this->storage->getAbsolutePath($receipt->file_path));
    }

    public function confirm(Request $request, Receipt $receipt)
    {
        if ($receipt->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
            'merchant_name' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_time' => 'nullable|date_format:H:i:s',
            'items' => 'nullable|array',
            'items.*.product_name' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.total_price' => 'required_with:items|numeric|min:0',
        ]);

        $idempotencyKey = $request->header('Idempotency-Key');
        if ($idempotencyKey) {
            $cacheKey = "receipt_confirm_{$receipt->id}_{$idempotencyKey}";
            if (Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey));
            }
        }

        $transaction = DB::transaction(function () use ($validated, $receipt, $request) {
            $lockedReceipt = Receipt::where('id', $receipt->id)->lockForUpdate()->first();

            if ($lockedReceipt->status !== ReceiptStatus::READY_FOR_REVIEW) {
                abort(response()->json(['success' => false, 'message' => 'Receipt is not ready for review or already confirmed.'], 400));
            }

            $transaction = $request->user()->transactions()->create([
                'category_id' => $validated['category_id'],
                'payment_method_id' => $validated['payment_method_id'],
                'receipt_id' => $lockedReceipt->id,
                'type' => TransactionType::EXPENSE, // Receipts are mostly expenses
                'amount' => $validated['amount'],
                'merchant_name' => $validated['merchant_name'],
                'transaction_date' => $validated['transaction_date'],
                'transaction_time' => $validated['transaction_time'] ?? null,
                'status' => TransactionStatus::COMPLETED,
            ]);

            if (isset($validated['items']) && is_array($validated['items'])) {
                $transaction->items()->createMany($validated['items']);
            }

            $lockedReceipt->update(['status' => ReceiptStatus::CONFIRMED]);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'CONFIRM_RECEIPT',
                'entity_type' => 'Receipt',
                'entity_id' => $lockedReceipt->id,
                'new_data' => $transaction->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $transaction;
        });

        $response = [
            'success' => true,
            'message' => 'Receipt confirmed and transaction created',
            'data' => $transaction->load(['items', 'category', 'paymentMethod'])
        ];

        if (isset($cacheKey)) {
            Cache::put($cacheKey, $response, now()->addHours(24));
        }

        return response()->json($response);
    }

    public function destroy(Request $request, Receipt $receipt)
    {
        if ($receipt->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $receipt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receipt deleted',
            'data' => null
        ], 204);
    }
}
