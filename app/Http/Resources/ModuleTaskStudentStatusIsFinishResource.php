<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleTaskStudentStatusIsFinishResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken(substr($request->header('authorization'), 7, 100))?->tokenable()->first();

        return [
            'id' => $this->id,
            'point' => $this->point,
            'is_finish' => $this->submissionTask()->where('user_id', $user?->id)->exists(),      
        ];
    }
}
