<?php

namespace App\Http\Controllers\Api;

use App\Domain\Audit\Services\AuditService;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\StoreTransferRequest;
use App\Http\Resources\TransferResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Transfer::query()
            ->with(['fromAccount', 'toAccount'])
            ->whereHas('fromAccount', function ($q) {
                $q->where('user_id', auth()->id());
            });

        $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

        return TransferResource::collection($query->paginate(15));
    }

    public function store(StoreTransferRequest $request): TransferResource
    {
        $transfer = DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $transfer = new Transfer($validated);
            $transfer->save();

            return $transfer;
        });

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'transfer_created',
            "Created transfer of amount {$transfer->amount} from account {$transfer->from_account_id} to {$transfer->to_account_id}",
            ['transfer_id' => $transfer->id]
        );

        $transfer->load(['fromAccount', 'toAccount']);

        return new TransferResource($transfer);
    }

    public function show(Transfer $transfer): TransferResource
    {
        $this->authorize('view', $transfer);

        $transfer->load(['fromAccount', 'toAccount']);

        return new TransferResource($transfer);
    }
}
