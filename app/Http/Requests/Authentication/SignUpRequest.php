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
            'password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
            'confirm_pass' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email sai format',
            'password.required' => 'Mật khẩu không được để trống',
            'password.regex' => 'Mật khẩu phải chứa ít nhất một chữ cái viết hoa, một chữ cái viết thường, một số và một ký tự đặc biệt.',
            'confirm_pass.required' => 'Xác nhận mật khẩu không được để trống',
            'password.min' => 'Mật khẩu dài ít nhất 6 ký tự',
            'confirm_pass.min' => 'Xác nhận mật khẩu dài ít nhất 6 ký tự',
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
                    $this->addError($validator, 'Email', 'Email đã tồn tại');
                }

                if ((string)$data['password'] !== (string)$data['confirm_pass']) {
                    $this->addError($validator, 'confirm_pass', 'Mật khẩu không khớp');
                }
            });
    }
}
