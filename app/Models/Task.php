<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id', 'title', 'description', 'status', 'priority', 'due_date', 'attachments'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'attachments' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignments');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
