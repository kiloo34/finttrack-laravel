<?php

namespace App\Modules\Account\Presentation\Http\Resources;

use App\Modules\Account\Application\DTOs\AccountOutput;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Account\Infrastructure\Services\AccountBalanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms AccountOutput DTO (or AccountModel) to HTTP JSON response.
 *
 * @mixin AccountOutput
 */
class AccountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $balanceService = app(AccountBalanceService::class);

        // Support both AccountOutput DTO and AccountModel for backward compat
        if ($this->resource instanceof AccountOutput) {
            return [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'type' => $this->resource->type,
                'currency' => $this->resource->currency,
                'account_number' => $this->resource->accountNumber,
                'provider_id' => $this->resource->providerId,
                'provider_name' => $this->resource->providerName,
                'initial_balance' => $this->resource->initialBalance,
                'current_balance' => (float) $balanceService->calculate(
                    $this->resource->id,
                    (string) $this->resource->initialBalance
                ),
                'is_active' => $this->resource->isActive,
                'created_at' => $this->resource->createdAt,
                'updated_at' => $this->resource->updatedAt,
            ];
        }

        // Fallback for AccountModel (e.g., when loading relations)
        /** @var AccountModel $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name,
            'type' => $model->type,
            'currency' => $model->currency,
            'account_number' => $model->account_number,
            'provider_id' => $model->provider_id,
            'provider_name' => $model->relationLoaded('provider') && $model->provider ? $model->provider->name : null,
            'initial_balance' => (float) $model->initial_balance,
            'current_balance' => (float) $balanceService->calculateFromModel($model),
            'is_active' => (bool) $model->is_active,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}
