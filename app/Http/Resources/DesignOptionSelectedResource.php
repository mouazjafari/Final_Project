<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignOptionSelectedResource extends JsonResource
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
            'option' => [
                'id' => $this->design_option_id,
                'name' => $this->designOption->option_name,
                'type' => $this->designOption->option_type,
                'description' => $this->designOption->description,
                'base_value' => $this->designOption->base_value,
                'allowed_values' => $this->designOption->allowed_values ?
                    json_decode($this->designOption->allowed_values) : null,
            ],
            'chosen_value' => $this->chosen_value,
            'additional_price' => $this->additional_price,
            'display_value' => $this->getDisplayValue(),
            'notes' => $this->notes,
        ];
    }
}
