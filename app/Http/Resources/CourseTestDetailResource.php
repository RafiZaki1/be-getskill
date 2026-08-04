<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTestDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    
        $this->loadMissing('course.courseLearningPaths.learningPath.division.classrooms');
        $classrooms = collect();

        foreach ($this->course->courseLearningPaths as $learningPath) {
            $division = $learningPath->learningPath->division ?? null;

            if ($division && $division->classrooms) {
                $classrooms = $classrooms->merge($division->classrooms);
            }
        }

        // Hapus duplikat berdasarkan ID
        $unique_classrooms = $classrooms->unique('id');

        // Filter berdasarkan request (name dan level)
        $filtered_classrooms = $unique_classrooms->filter(function ($classroom) use ($request) {
            $match = true;

            if ($request->name) {
                $match = $match && str_contains(strtolower($classroom->name), strtolower($request->name));
            }

            if ($request->level) {
                $match = $match && str_contains(strtolower($classroom->class_level), strtolower($request->level));
            }

            return $match;
        })->values();

        return [
            'classroom' => $filtered_classrooms,
        ];
    }
}
