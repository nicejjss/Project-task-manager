<?php

namespace App\Providers;

use App\Custom\Auth\CustomGuard;
use App\Custom\Auth\CustomProvider;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::extend('custom', function (Application $app, string $name, array $config) {
            return new CustomGuard(Auth::createUserProvider($config['provider']));
        });

        Auth::provider('custom', function (Application $app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new CustomProvider($config['model']);
        });

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            $avatar = $user->avatar;

            if ($avatar) {
                 if (!Str::contains($avatar, 'http')) {
                     $avatar = Storage::disk('gcs')->url($avatar);
                 }
            }

            $view->with([
                'user' => $user,
                'avatar' => $avatar,
                'key' => config('broadcasting.connections.pusher.key'),
            ]);
        });

        View::composer('layouts.sidebar', function ($view) {
            $currentProjectID = request()->route()->projectID;

            $currentProject = Project::where(
                'project_id', '=', $currentProjectID
            )->first(['project_id', 'project_name'])->toArray();

            $otherOwnProjects = Project::where([
                ['owner_id', '=', auth()->user()->id],
                ['project_id', '!=', $currentProjectID],
            ])->pluck('project_id')->toArray();

            $membersProject = ProjectMember::where('user_id', '=', auth()->user()->id)
            ->whereNotIn('project_id', array_merge([$currentProjectID], $otherOwnProjects))
            ->pluck('project_id')->unique()->toArray();

            $otherProjectsIds = array_merge($otherOwnProjects, $membersProject);

            $membersProject = Project::whereIn('projects.project_id', $otherProjectsIds)
                ->get(['projects.project_id', 'projects.project_name']);

            $view->with([
                'currentProject' => $currentProject,
                'otherProjects' => $membersProject->toArray(),
                'projectId' => $currentProjectID,
            ]);
        });
    }
}
