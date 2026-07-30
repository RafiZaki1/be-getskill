<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $createdAt = Carbon::parse($this->created_at); // Ganti $this->created_at dengan atribut tanggal pembuatan yang sesuai

        return [
            'user' => $this->user,
            'user_photo' => $this->user->photo !== null ? url('storage/' . $this->user->photo) : null,
            'course' => $this->course,
            'rating' => $this->rating,
            'review' => $this->review,
            'created' => $createdAt->diffForHumans(), // Menampilkan tanggal dalam format "X hari yang lalu"
        ];
    }
}
