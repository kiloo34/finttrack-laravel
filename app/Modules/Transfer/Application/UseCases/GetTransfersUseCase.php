<?php

namespace App\Modules\Transfer\Application\UseCases;

use App\Modules\Transfer\Application\DTOs\TransferOutput;
use App\Modules\Transfer\Domain\Repositories\TransferRepositoryInterface;

final class GetTransfersUseCase
{
    public function __construct(
        private readonly TransferRepositoryInterface $transferRepository,
    ) {}

    /**
     * @return array{data: TransferOutput[], total: int, per_page: int, current_page: int}
     */
    public function execute(int $userId, int $perPage = 15, int $page = 1): array
    {
        $result = $this->transferRepository->paginateForUser($userId, $perPage, $page);

        return [
            ...$result,
            'data' => array_map(
                fn ($t) => TransferOutput::fromDomain($t),
                $result['data'],
            ),
        ];
    }
}
