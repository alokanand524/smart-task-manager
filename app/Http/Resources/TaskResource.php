<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d H:i:s'),
            'is_overdue' => $this->due_date && $this->due_date->isPast() && $this->status !== 'completed',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Relationships
            'project' => new ProjectResource($this->whenLoaded('project')),
            'assignees' => UserResource::collection($this->whenLoaded('assignees')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            
            // Computed fields
            'assignees_count' => $this->whenLoaded('assignees', function () {
                return $this->assignees->count();
            }),
            'comments_count' => $this->whenLoaded('comments', function () {
                return $this->comments->count();
            }),
        ];
    }
}