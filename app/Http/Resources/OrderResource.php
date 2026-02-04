<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'address' => new AddressResource($this->address),
            // في OrderResource
            'designs' => $this->designOrders->map(function ($designOrder) {
                return [
                    'design' => $designOrder->design,
                    'quantity' => $designOrder->quantity,
                    'unit_price' => $designOrder->unit_price,
                    'options' => $designOrder->options, // ✅ من هون
                ];
            }),
            'notes' => $this->notes ?? "",

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
