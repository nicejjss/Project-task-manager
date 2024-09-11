<?php

namespace App\Http\Requests;

use App\Custom\Traits\JsonResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest extends FormRequest
{
    use JsonResponseTrait;
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
            //
        ];
    }

    public function validated($key = null, $default = null)
    {
        if ($key === null) {
            return parent::validated();
        }

        return parent::validated($key, $default);
    }

    public function failedValidation(Validator $validator)
    {
        $data = $validator->errors();
        throw new HttpResponseException($this->failed($data, 'Authentication failed'));
    }

    protected function addError(Validator $validator, string $field = null, string $message = null): \Illuminate\Support\MessageBag
    {
         return $validator->errors()->add($field, $message);
    }
}
