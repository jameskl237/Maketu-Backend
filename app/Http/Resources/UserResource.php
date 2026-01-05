<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'address' => $this->address,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            // Include related counts if available
            'shops_count' => $this->when(isset($this->shops_count), $this->shops_count),
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'orders_count' => $this->when(isset($this->orders_count), $this->orders_count),
            // Include related models if loaded
            'shops' => $this->whenLoaded('shops'),
            'products' => $this->whenLoaded('products'),
            'orders' => $this->whenLoaded('orders'),
        ];
    }
}
