<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'description' => $this->description,
            'source_account' => new AccountResource($this->whenLoaded('fromAccount')),
            'destination_account' => new AccountResource($this->whenLoaded('toAccount')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
