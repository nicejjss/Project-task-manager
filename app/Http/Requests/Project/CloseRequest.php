<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use App\Models\Project;

class CloseRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $projectID = (int)$this->route('projectID');
        $userID = auth()->user()->id;

        return Project::where([
            ['owner_id', '=', $userID],
            ['project_id', '=', $projectID],
        ])->exists();
    }

    protected function failedAuthorization(): false
    {
        return false;
    }
}
