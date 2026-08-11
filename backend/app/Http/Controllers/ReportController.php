<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $user = $request->user();
        $monthParam = $request->query('month', now()->format('Y-m'));
        $date = \Carbon\Carbon::createFromFormat('Y-m', $monthParam);
        
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $transactions = Transaction::where('user_id', $user->id)
            ->where('status', TransactionStatus::COMPLETED)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->get();

        $income = $transactions->where('type', TransactionType::INCOME)->sum('amount');
        $expense = $transactions->where('type', TransactionType::EXPENSE)->sum('amount');
        $saving = $income - $expense;

        // Category breakdown for expenses
        $expenseTransactions = $transactions->where('type', TransactionType::EXPENSE);
        $categoryBreakdown = [];
        
        $grouped = $expenseTransactions->groupBy('category_id');
        $categories = Category::whereIn('id', $grouped->keys())->get()->keyBy('id');

        foreach ($grouped as $categoryId => $groupTx) {
            $amount = $groupTx->sum('amount');
            $percentage = $expense > 0 ? ($amount / $expense) * 100 : 0;
            
            $categoryBreakdown[] = [
                'category_id' => $categoryId,
                'category_name' => $categories[$categoryId]->name ?? 'Unknown',
                'amount' => $amount,
                'percentage' => round($percentage, 2),
                'color' => $this->getColorForCategory($categoryId),
            ];
        }

        // Sort by amount descending
        usort($categoryBreakdown, fn($a, $b) => $b['amount'] <=> $a['amount']);

        return response()->json([
            'success' => true,
            'message' => 'Monthly report retrieved successfully',
            'data' => [
                'month' => $monthParam,
                'income' => $income,
                'expense' => $expense,
                'saving' => $saving,
                'categories' => $categoryBreakdown,
            ]
        ]);
    }
    
    private function getColorForCategory($id)
    {
        $colors = ['#f59e0b', '#3b82f6', '#ec4899', '#10b981', '#8b5cf6', '#ef4444', '#14b8a6'];
        return $colors[$id % count($colors)];
    }
}
