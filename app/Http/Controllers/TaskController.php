<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Events\TaskStatusChanged;
use App\Services\ActivityLogger;

class TaskController extends Controller
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'priority', 'project_id', 'assigned_to', 'search', 'per_page']);
        
        if ($request->user()->role->name === 'Employee') {
            $filters['assigned_to'] = $request->user()->id;
        }

        $tasks = $this->taskRepository->getTasksWithFilters($filters);
        
        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $taskData = $request->validated();
        
        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('task-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize()
                ];
            }
            $taskData['attachments'] = $attachments;
        }

        $task = $this->taskRepository->create($taskData);

        if ($request->assignee_ids) {
            $task->assignees()->sync($request->assignee_ids);
            
            // Send notifications to assignees
            foreach ($task->assignees as $assignee) {
                SendTaskNotification::dispatch($task, $assignee, 'assigned');
            }
        }

        ActivityLogger::log('task_created', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'project_id' => $task->project_id
        ], $request->user()->id);

        return new TaskResource($task->load(['project', 'assignees', 'comments']));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return new TaskResource($task->load(['project', 'assignees', 'comments.user']));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $oldStatus = $task->status;
        $task = $this->taskRepository->update($task->id, $request->validated());

        if ($request->has('assignee_ids')) {
            $task->assignees()->sync($request->assignee_ids);
        }

        // Fire event if status changed
        if ($request->status && $oldStatus !== $request->status) {
            event(new TaskStatusChanged($task, $oldStatus, $request->status, $request->user()));
        }

        return new TaskResource($task->load(['project', 'assignees', 'comments']));
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskRepository->delete($task->id);
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function assign(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->assignees()->syncWithoutDetaching([$request->user_id]);

        return response()->json(['message' => 'Task assigned successfully']);
    }

    public function unassign(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->assignees()->detach($request->user_id);

        return response()->json(['message' => 'Task unassigned successfully']);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json(['message' => 'Search query is required'], 400);
        }

        $tasks = $this->taskRepository->searchTasks($query);
        
        return TaskResource::collection($tasks);
    }
}