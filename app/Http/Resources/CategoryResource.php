<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $course_item_count = 0;
        foreach ($this->subCategories as $subCategories) {
            $course_item_count += $subCategories->courses->where('is_ready', true)->count();
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sub_category' => SubCategoryResource::collection($this->subCategories),
            'course_item_count' => $course_item_count
        ];
    }
}
