<?php

namespace App\Modules\Account\Infrastructure\Persistence\Repositories;

use App\Modules\Account\Domain\Entities\InvestmentAsset;
use App\Modules\Account\Domain\Repositories\InvestmentAssetRepositoryInterface;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Mappers\InvestmentAssetMapper;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\InvestmentAssetModel;
use Illuminate\Support\Collection;

final class EloquentInvestmentAssetRepository implements InvestmentAssetRepositoryInterface
{
    public function findById(int $id): ?InvestmentAsset
    {
        $model = InvestmentAssetModel::find($id);
        
        if (! $model) {
            return null;
        }

        return InvestmentAssetMapper::toDomain($model);
    }

    public function findByAccountId(int $accountId): Collection
    {
        return InvestmentAssetModel::where('account_id', $accountId)
            ->get()
            ->map(fn (InvestmentAssetModel $model) => InvestmentAssetMapper::toDomain($model));
    }

    public function save(InvestmentAsset $asset): void
    {
        $data = InvestmentAssetMapper::toPersistence($asset);
        
        if ($asset->getId() === 0) {
            $model = InvestmentAssetModel::create($data);
        } else {
            $model = InvestmentAssetModel::findOrFail($asset->getId());
            $model->update($data);
        }
    }

    public function delete(int $id): void
    {
        InvestmentAssetModel::destroy($id);
    }
}
