<?php

namespace App\Modules\Account\Infrastructure\Persistence\Eloquent\Mappers;

use App\Modules\Account\Domain\Entities\InvestmentAsset;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\InvestmentAssetModel;

final class InvestmentAssetMapper
{
    public static function toDomain(InvestmentAssetModel $model): InvestmentAsset
    {
        return InvestmentAsset::reconstitute(
            id: (int) $model->id,
            accountId: (int) $model->account_id,
            name: (string) $model->name,
            symbol: $model->symbol ? (string) $model->symbol : null,
            type: (string) $model->type,
            lots: $model->lots !== null ? (int) $model->lots : null,
            units: (string) $model->units,
            averageBuyPrice: (string) $model->average_buy_price,
            currentMarketPrice: (string) $model->current_market_price,
            lastSyncedAt: $model->last_synced_at ? new \DateTimeImmutable($model->last_synced_at->toDateTimeString()) : null,
            createdAt: new \DateTimeImmutable($model->created_at?->toDateTimeString() ?? 'now'),
            updatedAt: new \DateTimeImmutable($model->updated_at?->toDateTimeString() ?? 'now'),
        );
    }

    public static function toPersistence(InvestmentAsset $asset): array
    {
        return [
            'account_id' => $asset->getAccountId(),
            'name' => $asset->getName(),
            'symbol' => $asset->getSymbol(),
            'type' => $asset->getType(),
            'lots' => $asset->getLots(),
            'units' => $asset->getUnits(),
            'average_buy_price' => $asset->getAverageBuyPrice(),
            'current_market_price' => $asset->getCurrentMarketPrice(),
            'last_synced_at' => $asset->getLastSyncedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
