<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $stats = CacheService::remember(
            CacheService::dashboardStats($user->id),
            300,
            function () use ($user) {
                return [
                    'total_projects' => $this->getTotalProjects($user),
                    'total_tasks' => $this->getTotalTasks($user),
                    'completed_tasks' => $this->getCompletedTasks($user),
                    'pending_tasks' => $this->getPendingTasks($user),
                    'overdue_tasks' => $this->getOverdueTasks($user),
                ];
            }
        );

        if (in_array($user->role->name, ['Admin', 'Manager'])) {
            $stats['total_users'] = User::count();
            $stats['recent_activities'] = $this->getRecentActivities();
        }

        $stats['task_distribution'] = $this->getTaskDistribution($user);
        $stats['recent_tasks'] = $this->getRecentTasks($user);

        return response()->json($stats);
    }

    private function getTotalProjects($user)
    {
        if ($user->role->name === 'Employee') {
            return Project::whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orWhere('created_by', $user->id)->count();
        }
        return Project::count();
    }

    private function getTotalTasks($user)
    {
        if ($user->role->name === 'Employee') {
            return Task::whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();
        }
        return Task::count();
    }

    private function getCompletedTasks($user)
    {
        $query = Task::where('status', 'completed');
        if ($user->role->name === 'Employee') {
            $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        return $query->count();
    }

    private function getPendingTasks($user)
    {
        $query = Task::where('status', 'pending');
        if ($user->role->name === 'Employee') {
            $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        return $query->count();
    }

    private function getOverdueTasks($user)
    {
        $query = Task::where('due_date', '<', now())
                    ->whereIn('status', ['pending', 'in_progress']);
        if ($user->role->name === 'Employee') {
            $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        return $query->count();
    }

    private function getTaskDistribution($user)
    {
        $query = Task::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status');
        
        if ($user->role->name === 'Employee') {
            $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        return $query->get();
    }

    private function getRecentTasks($user)
    {
        $query = Task::with(['project', 'assignees'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5);
        
        if ($user->role->name === 'Employee') {
            $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        return $query->get();
    }

    private function getRecentActivities()
    {
        return Task::with(['project', 'assignees'])
                  ->orderBy('updated_at', 'desc')
                  ->limit(10)
                  ->get()
                  ->map(function ($task) {
                      return [
                          'id' => $task->id,
                          'title' => $task->title,
                          'project' => $task->project->name,
                          'status' => $task->status,
                          'updated_at' => $task->updated_at,
                      ];
                  });
    }
}