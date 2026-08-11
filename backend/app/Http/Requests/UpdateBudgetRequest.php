<?php
namespace App\Http\Requests;

class UpdateBudgetRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'period' => 'sometimes|string|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'nullable|date|required_if:period,custom',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:period,custom',
        ];
    }
}
