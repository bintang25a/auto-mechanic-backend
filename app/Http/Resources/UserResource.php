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
        $data = parent::toArray($request);
        $data['complaints'] = $this->complaints->map(function ($item) {
            return [
                'complaint_number' => $item->complaint_number,
                'vehicle' => $item->vehicle,
                'created_at' => $item->created_at,
                'queue' => [
                    'status' => $item->queue->status,
                ],
            ];
        });

        return $data;
    }
}
