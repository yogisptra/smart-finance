<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            'Cash', 'Bank Transfer', 'Debit Card', 'Credit Card', 'E-Wallet', 'QRIS', 'Other'
        ];

        foreach ($methods as $method) {
            PaymentMethod::create([
                'name' => $method,
                'type' => 'default',
            ]);
        }
    }
}
