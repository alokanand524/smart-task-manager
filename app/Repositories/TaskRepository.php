<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function getTasksByProject($projectId)
    {
        return Cache::remember("tasks_project_{$projectId}", 300, function () use ($projectId) {
            return $this->model->with(['assignees', 'comments'])
                              ->where('project_id', $projectId)
                              ->get();
        });
    }

    public function getTasksByUser($userId)
    {
        return $this->model->whereHas('assignees', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['project', 'assignees'])->get();
    }

    public function getOverdueTasks()
    {
        return $this->model->where('due_date', '<', now())
                          ->whereIn('status', ['pending', 'in_progress'])
                          ->with(['project', 'assignees'])
                          ->get();
    }

    public function searchTasks($query)
    {
        return $this->model->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%")
                          ->with(['project', 'assignees'])
                          ->get();
    }

    public function getTasksWithFilters(array $filters)
    {
        $query = $this->model->with(['project', 'assignees']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (isset($filters['assigned_to'])) {
            $query->whereHas('assignees', function ($q) use ($filters) {
                $q->where('user_id', $filters['assigned_to']);
            });
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }
}