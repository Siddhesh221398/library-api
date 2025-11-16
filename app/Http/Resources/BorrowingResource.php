<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name ?? null,
            ],
            'book' => [
                'id' => $this->book_id,
                'title' => $this->book->title ?? null,
                'status' => $this->book->status ?? null,
            ],
            'borrowed_at' => $this->borrowed_at?->toDateTimeString(),
            'returned_at' => $this->returned_at?->toDateTimeString(),
        ];
    }
}
