<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'currency',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categories() { return $this->hasMany(Category::class); }
    public function paymentMethods() { return $this->hasMany(PaymentMethod::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function receipts() { return $this->hasMany(Receipt::class); }
    public function budgets() { return $this->hasMany(Budget::class); }
}
