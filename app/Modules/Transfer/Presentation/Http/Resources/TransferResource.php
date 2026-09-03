<?php

namespace App\Modules\Transfer\Presentation\Http\Resources;

use App\Modules\Transfer\Application\DTOs\TransferOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TransferOutput */
class TransferResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var TransferOutput $output */
        $output = $this->resource;

        return [
            'id' => $output->id,
            'from_account_id' => $output->fromAccountId,
            'to_account_id' => $output->toAccountId,
            'amount' => $output->amount,
            'currency' => $output->currency,
            'transaction_date' => $output->transactionDate,
            'description' => $output->description,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ];
    }
}
