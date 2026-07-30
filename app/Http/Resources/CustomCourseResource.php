<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CustomCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $used = $this->courseLearningPaths()->where('course_id', $this->id)->whereRelation('learningPath', 'division_id', $request->division_id)->whereRelation('learningPath', 'class_level', $request->class_level)->first();
        $taskCount = 0;
        foreach ($this->modules as $module) {
            $taskCount += $module->moduleTasks->count();
        }
        return [
            'id' => $this->id,
            'module_count' => $this->modules->count(),
            'module_task_count' => $taskCount,
            'sub_category' => $this->subCategory,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'description' => $this->description,
            'is_premium' => $this->is_premium,
            'price' => $this->price,
            'promotional_price' => $this->promotional_price,
            'slug' => $this->slug,
            'photo' => ($this->photo && Storage::exists($this->photo)) ? url('storage/' . $this->photo) : null,
            'user' => $this->user,
            'is_ready' => $this->is_ready,
            'ratings' => $ratings = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0])
                ->merge(
                    $this->courseReviews->groupBy('rating')
                        ->map(fn($group) => $group->count())
                ),
            'rating' => $this->courseReviews->avg('rating') ?? 0,
            'status' => $used ? 'used' : 'unused',
            'step' => $used?->step ?? null,
            'course_test_id' => $this->courseTest?->id ?? null
        ];
    }
}
