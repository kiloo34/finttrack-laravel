<?php

namespace App\Modules\Account\Presentation\Http\Controllers;

use App\Modules\Account\Application\UseCases\CreateAccountUseCase;
use App\Modules\Account\Application\UseCases\DeleteAccountUseCase;
use App\Modules\Account\Application\UseCases\UpdateAccountUseCase;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Account\Presentation\Http\Requests\StoreAccountHttpRequest;
use App\Modules\Account\Presentation\Http\Requests\UpdateAccountHttpRequest;
use App\Modules\Account\Presentation\Http\Resources\AccountResource;
use App\Modules\Audit\Application\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AccountController
{
    public function __construct(
        private readonly CreateAccountUseCase $createUseCase,
        private readonly UpdateAccountUseCase $updateUseCase,
        private readonly DeleteAccountUseCase $deleteUseCase,
        private readonly AuditService $auditService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        /** @var int $userId */
        $userId = auth()->id();

        $accounts = AccountModel::query()
            ->with(['provider', 'investmentAssets'])
            ->where('user_id', $userId)
            ->get();

        return AccountResource::collection($accounts);
    }

    public function store(StoreAccountHttpRequest $request): JsonResponse
    {
        $output = $this->createUseCase->execute($request->toDto());

        $this->auditService->log('account_created', 'account', $output->id);

        return (new AccountResource($output))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();

        $account = AccountModel::query()
            ->with(['provider', 'investmentAssets'])
            ->where('id', $id)
            ->first();

        if (! $account || $account->user_id !== $userId) {
            abort(404, 'Account not found.');
        }

        return (new AccountResource($account))->response();
    }

    public function update(UpdateAccountHttpRequest $request, string $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();

        $output = $this->updateUseCase->execute(
            input: $request->toDto($id),
            actingUserId: $userId,
        );

        $this->auditService->log('account_updated', 'account', $output->id);

        return (new AccountResource($output))->response();
    }

    public function destroy(Request $request, string $id): Response
    {
        /** @var int $userId */
        $userId = auth()->id();

        $this->deleteUseCase->execute(
            accountId: AccountId::fromString($id),
            actingUserId: $userId,
            reason: $request->input('reason'),
        );

        $this->auditService->log('account_deleted', 'account', $id);

        return response()->noContent();
    }
}
