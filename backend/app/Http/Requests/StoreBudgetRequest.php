<?php
namespace App\Http\Requests;

class StoreBudgetRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|string|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'nullable|date|required_if:period,custom',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:period,custom',
        ];
    }
}
