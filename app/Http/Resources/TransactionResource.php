<?php

namespace App\Http\Resources;

use App\Domain\Transaction\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
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
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'description' => $this->description,
            'reference' => $this->reference,
            'status' => $this->status,
            'account' => new AccountResource($this->whenLoaded('account')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
