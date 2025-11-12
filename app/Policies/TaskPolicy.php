<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Task $task)
    {
        if (in_array($user->role->name, ['Admin', 'Manager'])) {
            return true;
        }

        return $task->assignees()->where('user_id', $user->id)->exists() ||
               $task->project->created_by === $user->id ||
               $task->project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user)
    {
        return in_array($user->role->name, ['Admin', 'Manager']);
    }

    public function update(User $user, Task $task)
    {
        if (in_array($user->role->name, ['Admin', 'Manager'])) {
            return true;
        }

        return $task->assignees()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Task $task)
    {
        if ($user->role->name === 'Admin') {
            return true;
        }

        return $task->project->created_by === $user->id;
    }

    public function assign(User $user, Task $task)
    {
        return in_array($user->role->name, ['Admin', 'Manager']) ||
               $task->project->created_by === $user->id;
    }
}