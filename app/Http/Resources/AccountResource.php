<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'account_code' => $this->code,
            'balance' => (float) $this->balance,
            'currency' => $this->currency->code,
            'settings' => $this->settings,
            'status' => $this->status != null ? $this->status : false,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
