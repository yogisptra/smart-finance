<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_id', 'name', 'quantity', 'unit_price',
        'discount', 'tax', 'total_price', 'confidence'
    ];

    public function receipt() { return $this->belongsTo(Receipt::class); }
}
