<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Course\BankModuleItemResource;
class BankModuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'school_year_id' => $this->school_year_id,
            'division_id' => $this->division_id,
            'class_level' => $this->class_level,
            'semester' => $this->semester,
            'modules' => BankModuleItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenLoaded('items', fn() => $this->items->count()),
        ];
    }
}
