<?php

namespace App\Modules\Account\Presentation\Http\Resources;

use App\Modules\Account\Domain\Entities\InvestmentAsset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InvestmentAsset
 */
class InvestmentAssetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var InvestmentAsset $asset */
        $asset = $this->resource;

        return [
            'id' => $asset->getId(),
            'account_id' => $asset->getAccountId(),
            'name' => $asset->getName(),
            'symbol' => $asset->getSymbol(),
            'type' => $asset->getType(),
            'lots' => $asset->getLots(),
            'units' => $asset->getUnits(),
            'average_buy_price' => $asset->getAverageBuyPrice(),
            'current_market_price' => $asset->getCurrentMarketPrice(),
            'portfolio_value' => $asset->getPortfolioValue(),
            'last_synced_at' => $asset->getLastSyncedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
