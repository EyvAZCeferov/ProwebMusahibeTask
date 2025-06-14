<?php

namespace App\Http\Resources;

use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BanknoteResource extends JsonResource
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
            'quantity' => $this->quantity,
            'status' => $this->status != null ? $this->status : true,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'creator' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
