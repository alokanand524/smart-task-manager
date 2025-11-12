<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Mail\TaskUpdatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTaskNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $user;
    protected $action;

    public function __construct(Task $task, User $user, string $action = 'updated')
    {
        $this->task = $task;
        $this->user = $user;
        $this->action = $action;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new TaskUpdatedMail($this->task, $this->user, $this->action));
    }
}