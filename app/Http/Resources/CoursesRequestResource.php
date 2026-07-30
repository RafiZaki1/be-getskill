<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursesRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'userid' => $this->user_id,
            'photo' => url('storage/' . $this->user->photo),
            'title' => $this->title,
            'occupation' => $this->occupation,
            'type' => $this->type,
            'price' => $this->price,
            'link' => $this->link,
            'status' => $this->status,
            'reason' => $this->admin_reason,
            'user_response' => $this->user_response,
        ];
    }
}
