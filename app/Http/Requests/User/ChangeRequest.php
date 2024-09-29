<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Validator;

class ChangeRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'required|min:6',
            'confirm_pass' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'confirm_pass.required' => 'Xác nhận mật khẩu không được để trống',
            'confirm_pass.min' => 'Xác nhận mật khẩu phải có ít nhất 6 ký tự',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function () use ($validator) {
            $data = $validator->getData();
            if ($data['password']!== $data['confirm_pass']) {
                $validator->errors()->add('confirm_pass', 'Mật khẩu không trùng khớp');
            }
        });
    }
}
