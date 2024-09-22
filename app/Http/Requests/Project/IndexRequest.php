<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Models\ProjectMember;
//use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $projectID = (int)$this->route('projectID');
        $userID = auth()->user()->id;

        $isOwner = Project::where([
            ['owner_id', '=', $userID],
            ['project_id', '=', $projectID],
        ])->exists();
        $isMember = ProjectMember::where([
            ['project_id', '=', $projectID],
            ['user_id', '=', $userID],
        ])->exists();

        return $isOwner || $isMember;
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
