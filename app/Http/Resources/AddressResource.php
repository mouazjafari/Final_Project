<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user->name ?? null,
            'city' => [
                'id' => $this->city->id ?? null,
                'name' => $this->city->getTranslations('name') ?? null,
            ],
            'area' => $this->area,
            'street' => $this->street,
            'latitude' => $this->Langitude ?? null,
            'longitude' => $this->Longitude ?? null,
            'notes' => $this->notes ?? null,
        ];
    }
}
