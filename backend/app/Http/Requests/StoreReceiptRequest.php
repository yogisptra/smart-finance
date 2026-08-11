<?php
namespace App\Http\Requests;

class StoreReceiptRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }
}
