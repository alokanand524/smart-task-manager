<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->whenLoaded('user')),
            'can_edit' => $request->user() && $request->user()->id === $this->user_id,
            'can_delete' => $request->user() && (
                $request->user()->id === $this->user_id || 
                in_array($request->user()->role->name, ['Admin', 'Manager'])
            ),
        ];
    }
}