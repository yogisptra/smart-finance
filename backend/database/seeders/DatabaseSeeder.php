<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Budget;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use App\Enums\PaymentMethodType;
use App\Enums\BudgetPeriod;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a dummy user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta'
            ]
        );

        // 2. Create standard Categories
        $categories = [
            ['name' => 'Food & Dining', 'type' => 'expense'],
            ['name' => 'Transportation', 'type' => 'expense'],
            ['name' => 'Entertainment', 'type' => 'expense'],
            ['name' => 'Shopping', 'type' => 'expense'],
            ['name' => 'Groceries', 'type' => 'expense'],
            ['name' => 'Salary', 'type' => 'income'],
            ['name' => 'Investment', 'type' => 'income'],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $created = Category::firstOrCreate([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'type' => $cat['type']
            ]);
            $categoryIds[$cat['name']] = $created->id;
        }

        // 3. Create Payment Methods
        $paymentMethods = [
            ['name' => 'Cash', 'type' => PaymentMethodType::CASH],
            ['name' => 'BCA', 'type' => PaymentMethodType::BANK],
            ['name' => 'GoPay', 'type' => PaymentMethodType::EWALLET],
        ];

        $paymentMethodIds = [];
        foreach ($paymentMethods as $pm) {
            $created = PaymentMethod::firstOrCreate([
                'user_id' => $user->id,
                'name' => $pm['name'],
                'type' => $pm['type']
            ]);
            $paymentMethodIds[$pm['name']] = $created->id;
        }

        // 4. Create Budgets
        Budget::firstOrCreate([
            'user_id' => $user->id,
            'category_id' => $categoryIds['Food & Dining']
        ], [
            'name' => 'Food Budget',
            'amount' => 2500000,
            'period' => BudgetPeriod::MONTHLY,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth()
        ]);

        Budget::firstOrCreate([
            'user_id' => $user->id,
            'category_id' => $categoryIds['Transportation']
        ], [
            'name' => 'Transport Budget',
            'amount' => 1000000,
            'period' => BudgetPeriod::MONTHLY,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth()
        ]);

        // 5. Generate dummy transactions for the current month
        $startOfMonth = now()->startOfMonth();
        $today = now();
        
        $transactions = [
            ['merchant' => 'Salary', 'amount' => 12000000, 'type' => TransactionType::INCOME, 'cat' => 'Salary', 'pm' => 'BCA', 'day' => 1],
            ['merchant' => 'Indomaret', 'amount' => 45000, 'type' => TransactionType::EXPENSE, 'cat' => 'Groceries', 'pm' => 'GoPay', 'day' => 2],
            ['merchant' => 'Pertamina', 'amount' => 300000, 'type' => TransactionType::EXPENSE, 'cat' => 'Transportation', 'pm' => 'Cash', 'day' => 3],
            ['merchant' => 'Starbucks', 'amount' => 65000, 'type' => TransactionType::EXPENSE, 'cat' => 'Food & Dining', 'pm' => 'GoPay', 'day' => 4],
            ['merchant' => 'KFC', 'amount' => 85000, 'type' => TransactionType::EXPENSE, 'cat' => 'Food & Dining', 'pm' => 'BCA', 'day' => 6],
            ['merchant' => 'Tokopedia', 'amount' => 450000, 'type' => TransactionType::EXPENSE, 'cat' => 'Shopping', 'pm' => 'BCA', 'day' => 10],
            ['merchant' => 'Gojek', 'amount' => 35000, 'type' => TransactionType::EXPENSE, 'cat' => 'Transportation', 'pm' => 'GoPay', 'day' => 11],
            ['merchant' => 'Freelance', 'amount' => 2500000, 'type' => TransactionType::INCOME, 'cat' => 'Salary', 'pm' => 'BCA', 'day' => 12],
            ['merchant' => 'Cinema XXI', 'amount' => 150000, 'type' => TransactionType::EXPENSE, 'cat' => 'Entertainment', 'pm' => 'GoPay', 'day' => 14],
        ];

        // Ensure we don't duplicate transactions constantly on seed
        Transaction::where('user_id', $user->id)->delete();

        foreach ($transactions as $t) {
            $txDate = $startOfMonth->copy()->addDays($t['day'] - 1);
            if ($txDate > $today) continue; // Don't seed future transactions
            
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $categoryIds[$t['cat']],
                'payment_method_id' => $paymentMethodIds[$t['pm']],
                'type' => $t['type'],
                'amount' => $t['amount'],
                'currency' => 'IDR',
                'merchant_name' => $t['merchant'],
                'transaction_date' => $txDate->format('Y-m-d'),
                'transaction_time' => '12:00:00',
                'status' => TransactionStatus::COMPLETED,
            ]);
        }
    }
}
