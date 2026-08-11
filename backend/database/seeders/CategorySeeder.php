<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            'Food & Beverage', 'Transportation', 'Shopping', 'Bills',
            'Entertainment', 'Health', 'Education', 'Travel',
            'Groceries', 'Household', 'Personal Care', 'Subscription', 'Others'
        ];

        $incomes = [
            'Salary', 'Bonus', 'Freelance', 'Business', 'Investment', 'Gift', 'Others'
        ];

        foreach ($expenses as $expense) {
            Category::create([
                'name' => $expense,
                'type' => 'expense',
                'is_default' => true,
            ]);
        }

        foreach ($incomes as $income) {
            Category::create([
                'name' => $income,
                'type' => 'income',
                'is_default' => true,
            ]);
        }
    }
}
