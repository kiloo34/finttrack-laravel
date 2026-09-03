<?php

namespace App\Modules\Transaction\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Transaction\Application\DTOs\CreateRecurringTransactionInput;
use App\Modules\Transaction\Application\DTOs\UpdateRecurringTransactionInput;
use App\Modules\Transaction\Application\UseCases\CreateRecurringTransactionUseCase;
use App\Modules\Transaction\Application\UseCases\DeleteRecurringTransactionUseCase;
use App\Modules\Transaction\Application\UseCases\GetRecurringTransactionsUseCase;
use App\Modules\Transaction\Application\UseCases\ProcessSingleRecurringTransactionUseCase;
use App\Modules\Transaction\Application\UseCases\UpdateRecurringTransactionUseCase;
use App\Modules\Transaction\Domain\ValueObjects\ExecutionType;
use App\Modules\Transaction\Domain\ValueObjects\RecurringFrequency;
use App\Modules\Transaction\Presentation\Http\Requests\StoreRecurringTransactionHttpRequest;
use App\Modules\Transaction\Presentation\Http\Requests\UpdateRecurringTransactionHttpRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function __construct(
        private readonly CreateRecurringTransactionUseCase $createUseCase,
        private readonly UpdateRecurringTransactionUseCase $updateUseCase,
        private readonly DeleteRecurringTransactionUseCase $deleteUseCase,
        private readonly GetRecurringTransactionsUseCase $getUseCase,
        private readonly ProcessSingleRecurringTransactionUseCase $processSingleUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['account_id', 'is_active']);
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $result = $this->getUseCase->paginateForUser(
            $request->user()->id,
            $filters,
            $perPage,
            $page
        );

        return response()->json($result);
    }

    public function store(StoreRecurringTransactionHttpRequest $request): JsonResponse
    {
        $input = new CreateRecurringTransactionInput(
            userId: $request->user()->id,
            accountId: (int) $request->validated('account_id'),
            destinationAccountId: $request->validated('destination_account_id') ? (int) $request->validated('destination_account_id') : null,
            categoryId: $request->validated('category_id') ? (int) $request->validated('category_id') : null,
            type: $request->validated('type'),
            amount: (string) $request->validated('amount'),
            currency: $request->validated('currency') ?? 'IDR',
            description: $request->validated('description'),
            frequency: RecurringFrequency::from($request->validated('frequency')),
            executionType: ExecutionType::from($request->validated('execution_type')),
            nextRunDate: CarbonImmutable::parse($request->validated('next_run_date')),
            isActive: (bool) $request->validated('is_active', true)
        );

        $output = $this->createUseCase->execute($input);

        return response()->json([
            'message' => 'Recurring transaction created successfully',
            'data' => (array) $output,
        ], 201);
    }

    public function update(UpdateRecurringTransactionHttpRequest $request, int $id): JsonResponse
    {
        $input = new UpdateRecurringTransactionInput(
            id: $id,
            userId: $request->user()->id,
            accountId: (int) $request->validated('account_id'),
            destinationAccountId: $request->validated('destination_account_id') ? (int) $request->validated('destination_account_id') : null,
            categoryId: $request->validated('category_id') ? (int) $request->validated('category_id') : null,
            type: $request->validated('type'),
            amount: (string) $request->validated('amount'),
            currency: $request->validated('currency') ?? 'IDR',
            description: $request->validated('description'),
            frequency: RecurringFrequency::from($request->validated('frequency')),
            executionType: ExecutionType::from($request->validated('execution_type')),
            nextRunDate: CarbonImmutable::parse($request->validated('next_run_date')),
            isActive: (bool) $request->validated('is_active', true)
        );

        $output = $this->updateUseCase->execute($input);

        return response()->json([
            'message' => 'Recurring transaction updated successfully',
            'data' => (array) $output,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->deleteUseCase->execute($request->user()->id, $id);

        return response()->json([
            'message' => 'Recurring transaction deleted successfully',
        ]);
    }

    public function execute(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'amount' => 'nullable|numeric|min:0.01',
            ]);

            $this->processSingleUseCase->execute($id, $validated['amount'] ?? null);

            return response()->json([
                'message' => 'Recurring transaction executed successfully',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (\LogicException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}
