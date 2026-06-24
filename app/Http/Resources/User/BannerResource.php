<?php

namespace App\Http\Resources\User;

use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => PublicStorage::url($this->image),
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
            'placement' => $this->placement,
            'order' => $this->order,
        ];
    }
}
