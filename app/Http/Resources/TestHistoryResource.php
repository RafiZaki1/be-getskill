<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestHistoryResource extends JsonResource
{
    public static function collection($resource)
    {
        // Filter data sebelum diubah ke array
        $filtered = $resource->filter(function ($item) {
            // hanya ambil jika user ada dan belum soft delete
            return $item->user && !$item->user->trashed();
        });

        return parent::collection($filtered);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->record_id,
            'user_name' => $this->user?->name,
            'user' => $this->user,
            'course_test' => $this->courseTest,
            'module_question_ids' => $this->module_question_id,
            'answer' => $this->answer,
            'pre_score' => $this->pre_score !== null
                        ? number_format($this->pre_score, 1)
                        : null,
            'post_score'=> $this->post_score !== null
                        ? number_format($this->post_score, 1)
                        : null,
        ];
    }
}
