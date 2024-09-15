<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ResetPasswordRequest extends BaseRequest
{

    private array $info;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'nullable|email:rfc',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email sai format',
        ];
    }

    public function validated($key = null, $default = null)
    {
        return $this->info;
    }

    /**
     * @throws ValidationException
     */
    public function withValidator(Validator $validator)
    {
            $validator->after(function () use ($validator) {
                $data = $validator->getData();
                $email = data_get($data, 'email');

                if ($email && !Auth::existEmail($email)) {
                        $this->addError($validator, 'Email', 'Email Chưa được đăng nhập');
                }

                $this->info['email'] = $email;
            });
    }
}
