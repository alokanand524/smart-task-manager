<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user)
    {
        return true; // All authenticated users can view projects
    }

    public function view(User $user, Project $project)
    {
        if (in_array($user->role->name, ['Admin', 'Manager'])) {
            return true;
        }

        return $project->created_by === $user->id || 
               $project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user)
    {
        return in_array($user->role->name, ['Admin', 'Manager']);
    }

    public function update(User $user, Project $project)
    {
        if ($user->role->name === 'Admin') {
            return true;
        }

        return $project->created_by === $user->id;
    }

    public function delete(User $user, Project $project)
    {
        if ($user->role->name === 'Admin') {
            return true;
        }

        return $project->created_by === $user->id;
    }

    public function manageMembers(User $user, Project $project)
    {
        if (in_array($user->role->name, ['Admin', 'Manager'])) {
            return true;
        }

        return $project->created_by === $user->id;
    }
}