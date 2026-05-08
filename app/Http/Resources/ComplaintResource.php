<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'complaint_number' => $this->complaint_number,

            'description' => $this->description,

            'created_at' => $this->created_at,

            'customer' => [
                'id' => $this->customer?->uid,
                'name' => $this->customer?->name,
            ],

            'queue' => new QueueResource($this->whenLoaded('queue')),

            'symptoms' => SymptomResource::collection($this->whenLoaded('symptoms')),

            'diagnosis' => $this->diagnosis,
        ];
    }
}
