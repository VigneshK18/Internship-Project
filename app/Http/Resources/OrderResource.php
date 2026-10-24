<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'subtotal' => (float) $this->subtotal,
            'shipping' => (float) $this->shipping,
            'total' => (float) $this->total,
            'payment_method' => $this->payment_method,
            'shipping_address' => [
                'name' => $this->customer_name,
                'phone' => $this->phone,
                'address_line' => $this->address_line,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'placed_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
