<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptOcrResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_id', 'raw_text', 'parsed_data', 'confidence_score',
        'provider', 'provider_request_id', 'error_message'
    ];

    protected function casts(): array
    {
        return [
            'parsed_data' => 'array',
        ];
    }

    public function receipt() { return $this->belongsTo(Receipt::class); }
}
