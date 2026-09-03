<?php

namespace App\Modules\Transaction\Presentation\Http\Resources;

use App\Modules\Transaction\Application\DTOs\RecurringTransactionOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RecurringTransactionOutput
 */
class RecurringTransactionResource extends JsonResource
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
            'user_id' => $this->userId,
            'account_id' => $this->accountId,
            'destination_account_id' => $this->destinationAccountId,
            'category_id' => $this->categoryId,
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'frequency' => $this->frequency,
            'execution_type' => $this->executionType,
            'next_run_date' => $this->nextRunDate,
            'is_active' => $this->isActive,
        ];
    }
}
