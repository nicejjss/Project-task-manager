<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class UpdateRequest extends BaseRequest
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
            'name' => 'required',
            'email' => 'required|email:rfc',
            'description' => 'nullable',
            'avatar' => 'nullable',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $data = Arr::flatten($validator->errors()->messages());
        throw new HttpResponseException(response()->json($data, 422));
    }

    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        $validator->after(function () use ($validator) {
            $data = $validator->getData();
            $changeEmail = data_get($data, 'email');

            $isExist = User::where('email', '=', $changeEmail)->where('id', '!=', auth()->user()->id)->exists();

            if($isExist) {
                $this->addError($validator, 'email', 'Email đã được sử dụng');
            }
        });
    }
}
