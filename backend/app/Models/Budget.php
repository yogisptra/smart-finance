<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'name', 'amount',
        'period', 'start_date', 'end_date'
    ];

    protected function casts(): array
    {
        return [
            'period' => \App\Enums\BudgetPeriod::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
}
