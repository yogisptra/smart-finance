<?php
namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionType;
use App\Models\Category;

class TransactionService
{
    public function create(array $data, User $user): Transaction
    {
        // 68. TRANSACTION CREATE BUSINESS RULE
        $category = Category::find($data['category_id'] ?? null);
        if ($category && $category->type !== $data['type']) {
            abort(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['category_id' => ['Category type must match transaction type.']]
            ], 422));
        }

        return DB::transaction(function () use ($data, $user) {
            $transaction = clone $user->transactions()->create($data);

            if (isset($data['items']) && is_array($data['items'])) {
                $transaction->items()->createMany($data['items']);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'CREATE_TRANSACTION',
                'entity_type' => 'Transaction',
                'entity_id' => $transaction->id,
                'new_data' => $transaction->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $transaction->load(['items', 'category', 'paymentMethod']);
        });
    }

    public function update(array $data, Transaction $transaction, User $user): Transaction
    {
        if (isset($data['category_id'])) {
            $category = Category::find($data['category_id']);
            $type = $data['type'] ?? $transaction->type->value;
            if ($category && $category->type !== $type) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['category_id' => ['Category type must match transaction type.']]
                ], 422));
            }
        }

        return DB::transaction(function () use ($data, $transaction, $user) {
            $oldData = clone $transaction;
            $transaction->update($data);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'UPDATE_TRANSACTION',
                'entity_type' => 'Transaction',
                'entity_id' => $transaction->id,
                'old_data' => $oldData->toArray(),
                'new_data' => $transaction->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $transaction->load(['category', 'paymentMethod']);
        });
    }

    public function delete(Transaction $transaction, User $user): void
    {
        DB::transaction(function () use ($transaction, $user) {
            $oldData = clone $transaction;
            $transaction->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'DELETE_TRANSACTION',
                'entity_type' => 'Transaction',
                'entity_id' => $transaction->id,
                'old_data' => $oldData->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        });
    }
}
