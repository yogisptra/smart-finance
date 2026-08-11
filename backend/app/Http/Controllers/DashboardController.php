<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Income & Expense for current month
        $totals = Transaction::where('user_id', $user->id)
            ->where('status', TransactionStatus::COMPLETED)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $income = $totals[TransactionType::INCOME->value] ?? 0;
        $expense = $totals[TransactionType::EXPENSE->value] ?? 0;
        
        // Balance is calculated from ALL TIME transactions
        $allTotals = Transaction::where('user_id', $user->id)
            ->where('status', TransactionStatus::COMPLETED)
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');
            
        $allIncome = $allTotals[TransactionType::INCOME->value] ?? 0;
        $allExpense = $allTotals[TransactionType::EXPENSE->value] ?? 0;
        $balance = $allIncome - $allExpense;

        // Recent transactions
        $recent = Transaction::where('user_id', $user->id)
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'balance' => $balance,
                'income' => $income,
                'expense' => $expense,
                'recent_transactions' => $recent,
            ]
        ]);
    }
}
