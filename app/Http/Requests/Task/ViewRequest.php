<?php

namespace App\Http\Requests\Task;

use App\Custom\Traits\PermissionTrait;
use App\Http\Requests\BaseRequest;

class ViewRequest extends BaseRequest
{
    use PermissionTrait;
    public function authorize(): bool
    {
        return $this->hasCreateTaskPermission(request('projectID'));
    }

    public function rules(): array
    {
       return [
           'title' => 'nullable|string',
           'sort' => 'nullable|integer',
           'assignee' => 'nullable|integer',
           'creator' => 'nullable|integer',
           'type' => 'nullable|integer',
           'status' => 'nullable|integer',
       ];
    }
}
