<?php

namespace App\Http\Resources\User;

use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => PublicStorage::url($this->path),
            'caption' => $this->caption,
            'is_primary' => $this->is_primary,
        ];
    }
}
