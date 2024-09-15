<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The mail field is required.',
            'password.required' => 'The password field is required.',
            'email.email' => 'Email phải đúng định dạng'
        ];
    }
}
