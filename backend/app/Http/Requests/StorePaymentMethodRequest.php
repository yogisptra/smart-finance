<?php
namespace App\Http\Requests;

class StorePaymentMethodRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ];
    }
}
