<?php

namespace App\Http\Resources\Certificate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            'code' => $this->code,
            'username' => $this->username,
            'certificate' => $this->certificate ? url('storage/' . $this->certificate) : null,
            'created' => $this->created_at->translatedFormat('d F Y'),
            'type' => $this->userCourse ? 'Course' : 'Event',
            'certificate_name' => $this->userCourse ? $this->userCourse->course->title : $this->userEvent->event->title,
        ];
    }
}
