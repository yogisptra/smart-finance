<?php
namespace App\Http\Requests;

class UpdateTransactionRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'type' => 'sometimes|string|in:income,expense',
            'amount' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'merchant_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'transaction_date' => 'sometimes|date',
            'transaction_time' => 'nullable|date_format:H:i:s',
            'status' => 'sometimes|string|in:pending,completed,cancelled',
        ];
    }
}
