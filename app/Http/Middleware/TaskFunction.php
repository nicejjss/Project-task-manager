<?php

namespace App\Http\Middleware;

use App\Custom\Traits\PermissionTrait;
use App\Models\Project;
use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskFunction
{
    use PermissionTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isProjectOwner = Project::where([['owner_id', '=', auth()->user()->id]])->exists();
        $hasPermission = Task::where([
            ['task_id', '=', $request->route('taskID')],
            ['created_by', '=', auth()->user()->id],
            ['project_id', '=', $request->route('projectID')],
        ])->orWhere([
            ['task_id', '=', $request->route('taskID')],
            ['assigned_to', '=', auth()->user()->id],
            ['project_id', '=', $request->route('projectID')],
        ])
            ->exists();

        if ($isProjectOwner || $hasPermission) {
            return $next($request);
        } else {
            return redirect()->back();
        }
    }
}
