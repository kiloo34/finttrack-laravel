<?php

namespace App\Modules\Transfer\Presentation\Http\Controllers;

use App\Modules\Audit\Application\Services\AuditService;
use App\Modules\Transfer\Application\UseCases\CreateTransferUseCase;
use App\Modules\Transfer\Application\UseCases\GetTransfersUseCase;
use App\Modules\Transfer\Application\UseCases\GetTransferByIdUseCase;
use App\Modules\Transfer\Presentation\Http\Requests\StoreTransferHttpRequest;
use App\Modules\Transfer\Presentation\Http\Resources\TransferResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TransferController
{
    public function __construct(
        private readonly CreateTransferUseCase $createUseCase,
        private readonly GetTransfersUseCase $getTransfersUseCase,
        private readonly GetTransferByIdUseCase $getTransferByIdUseCase,
        private readonly AuditService $auditService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var int $userId */
        $userId = auth()->id();

        $result = $this->getTransfersUseCase->execute(
            $userId,
            15,
            (int) $request->input('page', 1),
        );

        return TransferResource::collection(collect($result['data']));
    }

    public function store(StoreTransferHttpRequest $request): JsonResponse
    {
        $output = $this->createUseCase->execute($request->toDto());

        $this->auditService->log('transfer_created', 'transfer', $output->id);

        return (new TransferResource($output))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();

        $output = $this->getTransferByIdUseCase->execute($id, $userId);

        if (! $output) {
            abort(404, 'Transfer not found.');
        }

        return (new TransferResource($output))->response();
    }
}
