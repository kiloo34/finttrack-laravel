<?php

namespace App\Http\Controllers\Api;

use App\Domain\Audit\Services\AuditService;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Transaction::query()
            ->with(['account', 'category'])
            ->whereHas('account', function ($q) {
                $q->where('user_id', auth()->id());
            });

        // Filtering
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

        return TransactionResource::collection($query->paginate(15));
    }

    public function store(StoreTransactionRequest $request): TransactionResource
    {
        $transaction = DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $transaction = new Transaction($validated);
            // Default status if not provided
            $transaction->status = $validated['status'] ?? 'completed';
            $transaction->save();

            // Create LedgerEntry
            $direction = $transaction->type === 'expense' ? 'debit' : 'credit';
            if ($transaction->type === 'adjustment') {
                $direction = $transaction->amount >= 0 ? 'credit' : 'debit';
            }

            $transaction->ledgerEntries()->create([
                'account_id' => $transaction->account_id,
                'direction' => $direction,
                'amount' => abs((float) $transaction->amount),
                'currency' => $transaction->currency ?? 'IDR',
            ]);

            return $transaction;
        });

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'transaction_created',
            'Created transaction of amount: '.$transaction->amount,
            ['transaction_id' => $transaction->id]
        );

        $transaction->load(['account', 'category']);

        return new TransactionResource($transaction);
    }

    public function show(Transaction $transaction): TransactionResource
    {
        $this->authorize('view', $transaction);

        $transaction->load(['account', 'category']);

        return new TransactionResource($transaction);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): TransactionResource
    {
        $this->authorize('update', $transaction);

        DB::transaction(function () use ($request, $transaction) {
            $transaction->update($request->validated());

            // If amount or type changes, we need to update the ledger entry
            // In a real system, you might void the old and create new, but for simplicity we update it.
            if ($transaction->wasChanged(['amount', 'type', 'account_id'])) {
                $direction = $transaction->type === 'expense' ? 'debit' : 'credit';

                $transaction->ledgerEntries()->update([
                    'account_id' => $transaction->account_id,
                    'direction' => $direction,
                    'amount' => abs((float) $transaction->amount),
                ]);
            }
        });

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'transaction_updated',
            'Updated transaction: '.$transaction->id,
            ['transaction_id' => $transaction->id]
        );

        $transaction->load(['account', 'category']);

        return new TransactionResource($transaction);
    }

    public function destroy(Transaction $transaction): Response
    {
        $this->authorize('delete', $transaction);

        DB::transaction(function () use ($transaction) {
            $transaction->delete(); // This also soft-deletes ledger entries via model hook
        });

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'transaction_deleted',
            'Deleted transaction: '.$transaction->id,
            ['transaction_id' => $transaction->id]
        );

        return response()->noContent();
    }
}
