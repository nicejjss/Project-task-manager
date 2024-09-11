<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ActiveRequest extends BaseRequest
{

     private array|null $user_info;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'active_token' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'active_token.required' => 'The mail field is required.',
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
                $active_token = data_get($data, 'active_token', '_');
                if (!$this->user_info = Cache::get($active_token)) {
                    $this->addError($validator, 'Active Token', 'Token expired');
                }

                if (Auth::existEmail(data_get($this->user_info, 'email', '_'))) {
                    $this->addError($validator, 'Email', 'Email already signup, please use another');
                }

                $this->user_info['active_token'] = $active_token;
                Cache::forget($active_token);
            });
    }
}
