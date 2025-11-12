<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Will be handled by middleware
    }

    public function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date|after:today',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'exists:users,id',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'Please select a project',
            'title.required' => 'Task title is required',
            'priority.in' => 'Priority must be low, medium, or high',
            'due_date.after' => 'Due date must be in the future',
            'attachments.*.mimes' => 'Only PDF, DOC, DOCX, JPG, PNG files allowed',
            'attachments.*.max' => 'File size cannot exceed 2MB'
        ];
    }
}