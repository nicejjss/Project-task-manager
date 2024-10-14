<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class HomeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_name' => 'nullable',
            'project_status' => 'integer',
            'role' => 'integer',
            'user_mail' => 'nullable|email:rfc',
            'sort' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'user_mail.email' => 'Email không đúng định dạng',
        ];
    }

    public function validated($key = null, $default = null)
    {
        return parent::validated($key, $default);
    }

    public function failedValidation(Validator $validator)
    {
        parent::failedValidation($validator);
    }
}
