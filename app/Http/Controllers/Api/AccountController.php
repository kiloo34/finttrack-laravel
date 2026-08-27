<?php

namespace App\Http\Controllers\Api;

use App\Domain\Account\Models\Account;
use App\Domain\Audit\Services\AuditService;
use App\Domain\User\Models\User;
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
        $account->user_id = auth()->id();
        $account->save();

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'account_created',
            'Created account: '.$account->name,
            ['account_id' => $account->id]
        );

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

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'account_updated',
            'Updated account: '.$account->name,
            ['account_id' => $account->id]
        );

        return new AccountResource($account);
    }

    public function destroy(Account $account): Response
    {
        $this->authorize('delete', $account);

        $account->delete();

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'account_deleted',
            'Deleted account: '.$account->name,
            ['account_id' => $account->id]
        );

        return response()->noContent();
    }
}
