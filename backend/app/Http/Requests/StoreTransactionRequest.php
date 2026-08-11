<?php
namespace App\Http\Requests;

class StoreTransactionRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'receipt_id' => 'nullable|exists:receipts,id',
            'type' => 'required|string|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'merchant_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'transaction_time' => 'nullable|date_format:H:i:s',
            'status' => 'sometimes|string|in:pending,completed,cancelled',
            'items' => 'nullable|array',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.discount' => 'sometimes|numeric|min:0',
            'items.*.tax' => 'sometimes|numeric|min:0',
            'items.*.total_price' => 'required_with:items|numeric|min:0',
        ];
    }
}
