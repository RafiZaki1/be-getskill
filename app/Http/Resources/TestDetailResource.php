<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count_student' =>  $this->count(),
            'the_highest_score' => $this->orderBy('score', 'desc')->first()->score,
            'the_lowest_score' => $this->orderBy('score', 'asc')->first()->score,
        ];
    }
}
