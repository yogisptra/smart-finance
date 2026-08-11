<?php
namespace App\Http\Requests;

class LoginRequest extends BaseRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }
}
