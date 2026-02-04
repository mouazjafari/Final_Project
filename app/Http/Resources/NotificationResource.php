<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'type' => $this->type,
            'data' => $this->data,
            'read_at' => $this->read_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'is_read' => !is_null($this->read_at),
            'time_ago' => $this->created_at->diffForHumans(),
        ];
    }
}
