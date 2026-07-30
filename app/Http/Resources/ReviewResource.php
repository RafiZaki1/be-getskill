<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReviewResource extends JsonResource
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
            'id' => $this->id,
            'user' => $this->user,
            'course_title' => $this->course->title,
            'course_slug' => $this->course->slug,
            'course_photo' => ($this->course->photo && Storage::exists($this->course->photo)) ? url('storage/' . $this->course->photo) : null,
            'course_category' => $this->course->subCategory->name,
            'rating' => $this->rating,
            'review' => $this->review,
            'created' => $createdAt->diffForHumans(), // Menampilkan tanggal dalam format "X hari yang lalu"
        ];
    }
}
