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
            'email' => $this->email,
            'user_type' => $this->user_type,
            'preferred_language' => $this->preferred_language,
            'avatar_url' => $this->avatar_url,
            'stripe_account_id' => $this->stripe_account_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
