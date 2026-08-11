<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'payment_method_id', 'receipt_id',
        'type', 'amount', 'currency', 'merchant_name', 'description',
        'transaction_date', 'transaction_time', 'status'
    ];

    protected function casts(): array
    {
        return [
            'type' => \App\Enums\TransactionType::class,
            'status' => \App\Enums\TransactionStatus::class,
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
    public function receipt() { return $this->belongsTo(Receipt::class); }
    public function items() { return $this->hasMany(TransactionItem::class); }
}
