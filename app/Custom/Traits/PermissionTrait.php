<?php

namespace App\Custom\Traits;

use App\Models\Project;
use App\Models\ProjectMember;

trait PermissionTrait
{
   public function hasCreateTaskPermission($projectID): bool
   {
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
}
