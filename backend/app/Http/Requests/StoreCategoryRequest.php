<?php
namespace App\Http\Requests;

class StoreCategoryRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:income,expense',
            'icon' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ];
    }
}
