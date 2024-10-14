<?php

namespace App\Http\Requests\Project;

use App\Custom\Traits\PermissionTrait;
use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Models\ProjectMember;

class IndexRequest extends BaseRequest
{
    use PermissionTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->hasCreateTaskPermission(request('projectID'));
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

    public function validated($key = null, $default = null) {
        return $this->route('projectID');
    }
}
