<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('mechanic');

        return [
            'id' => $this->id,
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'mechanic_id' => $this->mechanic_id,
            'mechanic_name' => $user?->name,
            'mechanic_email' => $user?->email,
        ];
    }
}
