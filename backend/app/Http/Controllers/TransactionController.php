<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\AuditLog;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Services\Transaction\TransactionService;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $query = Transaction::where('user_id', $request->user()->id)->with(['category', 'paymentMethod']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merchant_name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        if ($request->has('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->has('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('transaction_date', 'asc')->orderBy('transaction_time', 'asc');
                break;
            case 'highest amount':
                $query->orderBy('amount', 'desc');
                break;
            case 'lowest amount':
                $query->orderBy('amount', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('transaction_date', 'desc')->orderBy('transaction_time', 'desc');
                break;
        }

        $transactions = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'message' => 'Transactions retrieved successfully',
            'data' => TransactionResource::collection($transactions)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ]
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->transactionService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => TransactionResource::make($transaction)
        ], 201);
    }

    public function show(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction retrieved successfully',
            'data' => TransactionResource::make($transaction->load(['items', 'category', 'paymentMethod', 'receipt']))
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $transaction = $this->transactionService->update(
            $request->validated(),
            $transaction,
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => TransactionResource::make($transaction)
        ]);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->transactionService->delete($transaction, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully',
            'data' => null
        ], 204);
    }

    public function export(Request $request)
    {
        $query = Transaction::where('user_id', $request->user()->id)
            ->with(['category', 'paymentMethod'])
            ->orderBy('transaction_date', 'desc');

        // Optional filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=transactions_" . now()->format('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($file, ['ID', 'Date', 'Time', 'Type', 'Amount', 'Currency', 'Merchant', 'Category', 'Payment Method', 'Status']);

            $query->chunk(500, function ($transactions) use ($file) {
                foreach ($transactions as $tx) {
                    fputcsv($file, [
                        $tx->id,
                        $tx->transaction_date,
                        $tx->transaction_time,
                        $tx->type->value ?? $tx->type,
                        $tx->amount,
                        $tx->currency,
                        $tx->merchant_name,
                        $tx->category->name ?? '',
                        $tx->paymentMethod->name ?? '',
                        $tx->status->value ?? $tx->status
                    ]);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
