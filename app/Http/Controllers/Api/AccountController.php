<?php

namespace App\Http\Controllers\Api;

use App\Domain\Account\Models\Account;
use App\Domain\Audit\Services\AuditService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(): AnonymousResourceCollection
    {
        $accounts = Account::query()
            ->where('user_id', auth()->id())
            ->get();

        return AccountResource::collection($accounts);
    }

    public function store(StoreAccountRequest $request): AccountResource
    {
        $account = new Account($request->validated());
        /** @var int<1, max> $userId */
        $userId = auth()->id();
        $account->user_id = $userId;
        $account->save();

        $this->auditService->log('account_created', $account);

        return new AccountResource($account);
    }

    public function show(Account $account): AccountResource
    {
        $this->authorize('view', $account);

        return new AccountResource($account);
    }

    public function update(UpdateAccountRequest $request, Account $account): AccountResource
    {
        $this->authorize('update', $account);

        $account->update($request->validated());

        $this->auditService->log('account_updated', $account, $account->getOriginal() ?: [], $account->getAttributes());

        return new AccountResource($account);
    }

    public function destroy(Account $account): Response
    {
        $this->authorize('delete', $account);

        $account->delete();

        $this->auditService->log('account_deleted', $account, $account->getAttributes());

        return response()->noContent();
    }
}
