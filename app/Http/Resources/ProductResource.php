<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "book_title"=>$this->title,
            "author"=>$this->author,
            "description"=>$this->description,
            "price"=>$this->price,
            "book_image"=>$this->image,
            "discount"=>$this->discount ? $this->discount->precentage : null,
        ];
    }
    
}
