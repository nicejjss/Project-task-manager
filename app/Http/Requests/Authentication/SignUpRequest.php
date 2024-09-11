<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class SignUpRequest extends BaseRequest
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
            'confirm_pass' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The mail field is required.',
            'email.email' => 'The mail field is wrong format.',
            'password.required' => 'The password field is required.',
            'confirm_pass.required' => 'The confirm password field is required.',
        ];
    }

    /**
     * @throws ValidationException
     */
    public function withValidator(Validator $validator)
    {
            $validator->after(function () use ($validator) {
                $data = $validator->getData();

                if (Auth::existEmail(data_get($data, 'email', '_'))) {
                    $this->addError($validator, 'Email', 'Email already exists');
                }

                if ((string)$data['password'] !== (string)$data['confirm_pass']) {
                    $this->addError($validator, 'confirm_pass', 'Passwords do not match');
                }
            });
    }
}
