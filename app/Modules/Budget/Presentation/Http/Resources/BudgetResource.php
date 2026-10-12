<?php

namespace App\Modules\Budget\Presentation\Http\Resources;

use App\Modules\Budget\Application\DTOs\BudgetOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BudgetOutput */
class BudgetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var BudgetOutput $output */
        $output = $this->resource;

        return [
            'id' => $output->id,
            'category_id' => $output->categoryId,
            'category_name' => $output->categoryName,
            'amount' => $output->amount,
            'spent_amount' => $output->spentAmount,
            'remaining_amount' => $output->remainingAmount,
            'percentage' => $output->percentage,
            'month' => $output->month,
            'year' => $output->year,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ];
    }
}

