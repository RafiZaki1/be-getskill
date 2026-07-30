<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'has_pre_test' => $this->has_pre_test,
            'has_post_test' => $this->has_post_test,
            'status' => $this->has_pre_test && $this->has_post_test ? 'Selesai' : 'Belum Selesai',
            'created_at' => $this->created_at,
            'sub_module_slug' => $this->subModule->slug,
        ];
    }
}
