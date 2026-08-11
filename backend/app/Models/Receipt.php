<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'file_name', 'file_path', 'file_size',
        'mime_type', 'status', 'uploaded_at', 'processed_at'
    ];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\ReceiptStatus::class,
            'uploaded_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function transaction() { return $this->hasOne(Transaction::class); }
    public function items() { return $this->hasMany(ReceiptItem::class); }
    public function ocrResult() { return $this->hasOne(ReceiptOcrResult::class); }
}
