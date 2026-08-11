<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'merchant_name' => $this->merchant_name,
            'description' => $this->description,
            'transaction_date' => $this->transaction_date,
            'transaction_time' => $this->transaction_time,
            'status' => $this->status,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'icon' => $this->category->icon,
                ];
            }),
            'payment_method' => $this->whenLoaded('paymentMethod', function () {
                return [
                    'id' => $this->paymentMethod->id,
                    'name' => $this->paymentMethod->name,
                    'type' => $this->paymentMethod->type,
                ];
            }),
            'items' => $this->whenLoaded('items'),
            'receipt' => $this->whenLoaded('receipt'),
        ];
    }
}
