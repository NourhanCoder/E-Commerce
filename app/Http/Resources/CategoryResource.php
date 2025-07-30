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
        // لو ده تصنيف فرعي
        if ($this->parent) {
            return [
                'main_category' => $this->parent->name,
                'sub_category' => $this->name,
            ];
        }

        // لو ده تصنيف رئيسي
        return [
            'main_category' => $this->name,
            'sub_categories' => $this->children->pluck('name'),
        ];
    }
}
