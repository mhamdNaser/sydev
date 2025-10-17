<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IconResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_name' => $this->category ? $this->category->name : null,
            'category_id' => $this->category_id,
            'is_premium' => $this->is_premium,
            'is_active' => $this->is_active,
            'icon_text' => $this->file_svg ? $this->file_svg : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

