<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'data' => $this->data ?? (object) [],
            'is_read' => (bool) $this->is_read,
            'status' => $this->status,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
        ];
    }
}
