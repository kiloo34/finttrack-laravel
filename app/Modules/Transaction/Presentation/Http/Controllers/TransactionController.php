<?php

namespace App\Modules\Transaction\Presentation\Http\Controllers;

use App\Modules\Audit\Application\Services\AuditService;
use App\Modules\Transaction\Application\UseCases\CreateTransactionUseCase;
use App\Modules\Transaction\Application\UseCases\DeleteTransactionUseCase;
use App\Modules\Transaction\Application\UseCases\GetTransactionByIdUseCase;
use App\Modules\Transaction\Application\UseCases\GetTransactionsUseCase;
use App\Modules\Transaction\Application\UseCases\UpdateTransactionUseCase;
use App\Modules\Transaction\Presentation\Http\Requests\StoreTransactionHttpRequest;
use App\Modules\Transaction\Presentation\Http\Requests\UpdateTransactionHttpRequest;
use App\Modules\Transaction\Presentation\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class TransactionController
{
    public function __construct(
        private readonly CreateTransactionUseCase $createUseCase,
        private readonly UpdateTransactionUseCase $updateUseCase,
        private readonly DeleteTransactionUseCase $deleteUseCase,
        private readonly GetTransactionsUseCase $getTransactionsUseCase,
        private readonly GetTransactionByIdUseCase $getTransactionByIdUseCase,
        private readonly AuditService $auditService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var int $userId */
        $userId = auth()->id();

        $filters = $request->only([
            'account_id', 'category_id', 'type', 'start_date', 'end_date',
            'search', 'min_amount', 'max_amount', 'status',
        ]);
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $result = $this->getTransactionsUseCase->execute($userId, $filters, $perPage, (int) $request->input('page', 1));

        return TransactionResource::collection(collect($result['data']))->additional([
            'meta' => [
                'current_page' => $result['current_page'],
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'last_page' => max((int) ceil($result['total'] / max($result['per_page'], 1)), 1),
            ],
        ]);
    }

    public function store(StoreTransactionHttpRequest $request): JsonResponse
    {
        $output = $this->createUseCase->execute($request->toDto());

        $this->auditService->log('transaction_created', 'transaction', $output->id);

        return (new TransactionResource($output))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();

        $output = $this->getTransactionByIdUseCase->execute($id, $userId);

        if (! $output) {
            abort(404, 'Transaction not found.');
        }

        return (new TransactionResource($output))->response();
    }

    public function update(UpdateTransactionHttpRequest $request, int $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();

        $output = $this->updateUseCase->execute(
            input: $request->toDto($id),
            actingUserId: $userId,
        );

        $this->auditService->log('transaction_updated', 'transaction', $output->id);

        return (new TransactionResource($output))->response();
    }

    public function destroy(int $id): Response
    {
        $this->deleteUseCase->execute($id);

        $this->auditService->log('transaction_deleted', 'transaction', $id);

        return response()->noContent();
    }
}
