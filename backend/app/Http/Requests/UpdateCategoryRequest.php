<?php
namespace App\Http\Requests;

class UpdateCategoryRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:income,expense',
            'icon' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ];
    }
}
