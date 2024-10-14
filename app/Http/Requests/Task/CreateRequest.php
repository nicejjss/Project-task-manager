<?php

namespace App\Http\Requests\Task;

use App\Custom\Traits\PermissionTrait;
use App\Http\Requests\BaseRequest;

class CreateRequest extends BaseRequest
{
    use PermissionTrait;
    public function authorize(): bool
    {
        return $this->hasCreateTaskPermission(request('projectID'));
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => 'nullable',
            'title' => 'required',
            'assignee' => 'required|exists:users,id',
            'priority' => 'required',
            'tasktype' => 'nullable',
            'deadline' => 'nullable|date',
            'attachments' => 'nullable',
        ];
    }

    /**
     * Get custom messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Tên công việc là bắt buộc',
            'assignee.required' => 'Người thực hiện là bắt buộc',
            'priority.required' => 'Độ ưu tiên là bắt buộc',
            'deadline.date' => 'Hạn chót phải là ngày hợp lệ',
        ];
    }
}
