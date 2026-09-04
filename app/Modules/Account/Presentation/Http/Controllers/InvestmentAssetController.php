<?php

namespace App\Modules\Account\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Domain\Entities\InvestmentAsset;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\Repositories\InvestmentAssetRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Account\Presentation\Http\Resources\InvestmentAssetResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentAssetController extends Controller
{
    public function __construct(
        private readonly InvestmentAssetRepositoryInterface $assetRepository,
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function index(int $accountId): JsonResponse
    {
        $account = $this->accountRepository->findById(AccountId::fromString((string) $accountId));
        if (!$account || $account->getUserId() !== auth()->id()) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $assets = $this->assetRepository->findByAccountId($accountId);

        return response()->json(InvestmentAssetResource::collection($assets->values()));
    }

    public function store(Request $request, int $accountId): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
            'type' => 'required|string|in:stock,mutual_fund,crypto,bond,other',
            // Stock: lots (integer, 1 lot = 100 shares)
            'lots' => 'nullable|integer|min:1',
            // Mutual fund / others: decimal units
            'units' => 'nullable|numeric|min:0',
            'average_buy_price' => 'required|numeric|min:0',
            'current_market_price' => 'required|numeric|min:0',
        ]);

        $account = $this->accountRepository->findById(AccountId::fromString((string) $accountId));
        if (!$account || $account->getUserId() !== auth()->id()) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        // Determine units based on type
        $lots = isset($validated['lots']) ? (int) $validated['lots'] : null;
        $units = $lots !== null ? (string) ($lots * 100) : (string) ($validated['units'] ?? 0);

        $asset = InvestmentAsset::create(
            accountId: $accountId,
            name: $validated['name'],
            type: $validated['type'],
            units: $units,
            averageBuyPrice: (string) $validated['average_buy_price'],
            currentMarketPrice: (string) $validated['current_market_price'],
            symbol: $validated['symbol'] ?? null,
            lots: $lots,
        );

        $this->assetRepository->save($asset);

        return response()->json(['message' => 'Asset added successfully'], 201);
    }

    public function syncPrices(Request $request, int $accountId): JsonResponse
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*.asset_id' => 'required|integer',
            'prices.*.current_market_price' => 'required|numeric',
        ]);

        $account = $this->accountRepository->findById(AccountId::fromString((string) $accountId));
        if (!$account || $account->getUserId() !== auth()->id()) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        foreach ($validated['prices'] as $priceData) {
            $asset = $this->assetRepository->findById($priceData['asset_id']);
            if ($asset && $asset->getAccountId() === $accountId) {
                $asset->updateMarketPrice((string) $priceData['current_market_price']);
                $this->assetRepository->save($asset);
            }
        }

        return response()->json(['message' => 'Asset prices synced successfully']);
    }
}
