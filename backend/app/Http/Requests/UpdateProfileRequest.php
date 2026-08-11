<?php
namespace App\Http\Requests;

class UpdateProfileRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $this->user()->id,
            'currency' => 'sometimes|string|size:3',
            'timezone' => 'sometimes|string',
        ];
    }
}
