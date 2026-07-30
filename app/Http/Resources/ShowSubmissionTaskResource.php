<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowSubmissionTaskResource extends JsonResource
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
            'user' => $this->user,
            'module_task' => $this->moduleTask,
            'answer' => $this->answer,
            'file' => $this->file,
            'created_at' => Carbon::parse($this->created_at)->isoFormat('DD MMMM YYYY'),
            'updated_at' => Carbon::parse($this->updated_at)->isoFormat('DD MMMM YYYY'),
        ];
    }
}
