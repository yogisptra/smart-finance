<?php
namespace App\Http\Requests;

class UpdatePaymentMethodRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string',
        ];
    }
}
