<?php

namespace App\Http\Controllers\WEB;

use App\Http\Requests\Project\IndexRequest;
use App\Services\ProjectServices;
use Illuminate\Support\Facades\Log;

class ProjectController extends BaseController
{
    private ProjectServices $projectServices;

    public function __construct(ProjectServices $projectServices)
    {
        $this->projectServices = $projectServices;
    }

    public function index(IndexRequest $request) {
        $data = $this->projectServices->index((array)$request->validated());
        return view('project.index', [
            'projectId' => $data['projectId'],
            'project' => $data['projectDescription'],
            'tasks' => $data['tasks'],
            'members' =>$data['members'],
            'ownerId' => $data['ownerId'],
        ]);
    }

    public function createIndex() {
        return view('project.create');
    }

    public function store() {
        try {
            return $this->projectServices->store(request()->all());
        } catch (\Exception  $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function invite(string $projectID)
    {
        $projectID = (int)$projectID;

        if ($this->projectServices->invite($projectID)) {
            return redirect('project/' . $projectID);
        }

        return redirect('/');
    }

    public function addMember()
    {
        $msgT = $this->projectServices->addMember(request()->get('email'), request()->get('projectID'));

        switch ($msgT) {
            case 1:
            case 2: return 'Đã gửi mail';
            case 3: return 'Thành viên đã có trong dự án';
            default: return 'Gửi mail thất bại';
        }
    }

    public function editView(string $projectID)
    {
        $projectID = (int)$projectID;
        $data = $this->projectServices->get($projectID);
        return view('project.edit', $data);
    }

    public function edit()
    {
//        return view('project.edit');
    }
}
