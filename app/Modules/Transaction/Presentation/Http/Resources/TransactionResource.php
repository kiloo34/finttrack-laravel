<?php

namespace App\Modules\Transaction\Presentation\Http\Resources;

use App\Modules\Transaction\Application\DTOs\TransactionOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TransactionOutput */
class TransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var TransactionOutput $output */
        $output = $this->resource;

        return [
            'id' => $output->id,
            'type' => $output->type,
            'amount' => $output->amount,
            'currency' => $output->currency,
            'transaction_date' => $output->transactionDate,
            'description' => $output->description,
            'reference' => $output->reference,
            'status' => $output->status,
            'account_id' => $output->accountId,
            'category_id' => $output->categoryId,
            'attachment_url' => $output->attachmentUrl,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ];
    }
}
