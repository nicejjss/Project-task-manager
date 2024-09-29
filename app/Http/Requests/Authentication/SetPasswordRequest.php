<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Validator;

class SetPasswordRequest extends BaseRequest
{
    private array $user_info;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'nullable|min:6',
            'confirm_pass' => 'nullable|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'Mật khẩu dài ít nhất 6 ký tự',
        ];
    }

    public function validated($key = null, $default = null)
    {
        return $this->user_info;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function () use ($validator) {
            $data = $validator->getData();
            if ($reset_token = data_get($data, 'reset_token')) {
                if (!$mail = Cache::get($reset_token)) {
                    $this->addError($validator, 'Active Token', 'Token hết hạn');
                }

                $this->user_info['email'] = $mail;

                Cache::forget($reset_token);
            } else {
                $email = data_get($data, 'email');
                $newPassword = (string)data_get($data, 'password', '');
                $confirm_pass = (string)data_get($data, 'confirm_pass', '_');

                if (empty($newPassword) || $newPassword !== $confirm_pass) {
                    $this->addError($validator, 'password', 'Không có xác thực');
                }

                $this->user_info['email'] = $email;
                $this->user_info['confirm_pass'] = $newPassword;
            }
        });
    }
}
