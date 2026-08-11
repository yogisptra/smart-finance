<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $budgets = Budget::where('user_id', $user->id)->with('category')->get();
        
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Calculate usage for each budget
        $budgetData = $budgets->map(function ($budget) use ($user, $startOfMonth, $endOfMonth) {
            $used = Transaction::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->where('type', TransactionType::EXPENSE)
                ->where('status', TransactionStatus::COMPLETED)
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
                
            return [
                'id' => $budget->id,
                'name' => $budget->category->name ?? 'Unknown',
                'limit' => $budget->amount,
                'used' => $used,
                'period' => $budget->period,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Budgets retrieved successfully',
            'data' => $budgetData
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|string',
        ]);

        $budget = $request->user()->budgets()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Budget created',
            'data' => $budget
        ], 201);
    }
}
