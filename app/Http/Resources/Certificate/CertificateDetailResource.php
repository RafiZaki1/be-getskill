<?php

namespace App\Http\Resources\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data['user'] = [];

        if($this->userCourse) {
            $data['course'] = CourseResource::make($this->userCourse->course);
            $data['user'] = UserResource::make($this->userCourse->user);
        } else {
            $data['event'] = EventResource::make($this->userEvent->event);
            $data['user'] = UserResource::make($this->userEvent->user);
        }
            
        return [
            'id' => $this->id,
            'code' => $this->code,
            'username' => $this->username,
            'certificate' => $this->certificate ? url('storage/' . $this->certificate) : null,
            ...$data,
            'created' => $this->created_at,
            'valid_until' => $this->expired_at,
        ];
    }
}
