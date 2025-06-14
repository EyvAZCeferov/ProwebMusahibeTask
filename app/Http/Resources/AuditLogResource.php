<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'method' => $this->method,
            'url' => $this->url,
            'status_code' => $this->status_code,
            'ip_address' => $this->ip_address,
            'latency' => $this->latency_ms . ' ms',
            'requested_at' => $this->created_at->format('Y-m-d H:i:s'),

            'user_agent_details' => [
                'browser' => $this->user_agent['browser'] ?? 'N/A',
                'platform' => $this->user_agent['platform'] ?? 'N/A',
                'full_string' => $this->user_agent['original'] ?? 'N/A',
            ],

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }, 'Guest'),
        ];
    }
}
