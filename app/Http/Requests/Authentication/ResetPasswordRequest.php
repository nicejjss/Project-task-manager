<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ResetPasswordRequest extends BaseRequest
{

    private array $user_info;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'nullable|email:rfc',
            'password' => 'nullable',
            'confirm_pass' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The mail field is required.',
            'email.email' => 'The mail field is wrong format.',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return $this->user_info;
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
                        $this->addError($validator, 'Email', 'Email is not registered');
                }

                $this->user_info['email'] = $email;

                if ($reset_token = data_get($data, 'reset_token')) {
                    $newPassword = (string)data_get($data, 'password', '');
                    $confirm_pass = (string)data_get($data, 'confirm_pass', '_');

                    if (empty($newPassword) || $newPassword !== $confirm_pass) {
                        $this->addError($validator, 'password', 'Wrong to confirm password, check again');
                    }

                    if (!$mail = Cache::get($reset_token)) {
                        $this->addError($validator, 'Active Token', 'Token expired');
                    }

                    $this->user_info['email'] = $mail;
                    $this->user_info['new_password'] = $newPassword;
                    Cache::forget($reset_token);
                }
            });
    }
}
