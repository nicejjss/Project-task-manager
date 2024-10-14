<?php

namespace App\Http\Middleware;

use App\Custom\Traits\PermissionTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskPermission
{
    use PermissionTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->hasCreateTaskPermission((int) $request->route('projectID'))) {
            return $next($request);
        } else {
            return redirect()->back();
        }
    }
}
