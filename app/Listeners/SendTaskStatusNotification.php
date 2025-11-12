<?php

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use App\Jobs\SendTaskNotification;
use App\Services\ActivityLogger;

class SendTaskStatusNotification
{
    public function handle(TaskStatusChanged $event)
    {
        // Send notifications to all assignees
        foreach ($event->task->assignees as $assignee) {
            if ($assignee->id !== $event->user->id) {
                SendTaskNotification::dispatch($event->task, $assignee, 'status_updated');
            }
        }

        // Log activity
        ActivityLogger::log('task_status_changed', [
            'task_id' => $event->task->id,
            'task_title' => $event->task->title,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'changed_by' => $event->user->name
        ], $event->user->id);
    }
}