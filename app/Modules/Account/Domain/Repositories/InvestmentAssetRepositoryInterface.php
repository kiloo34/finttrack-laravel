<?php

namespace App\Modules\Account\Domain\Repositories;

use App\Modules\Account\Domain\Entities\InvestmentAsset;
use Illuminate\Support\Collection;

interface InvestmentAssetRepositoryInterface
{
    public function findById(int $id): ?InvestmentAsset;
    
    /**
     * @return Collection<int, InvestmentAsset>
     */
    public function findByAccountId(int $accountId): Collection;

    public function save(InvestmentAsset $asset): void;

    public function delete(int $id): void;
}
