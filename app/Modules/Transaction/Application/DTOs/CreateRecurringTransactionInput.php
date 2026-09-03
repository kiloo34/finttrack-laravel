<?php

namespace App\Modules\Transaction\Application\DTOs;

use App\Modules\Transaction\Domain\ValueObjects\ExecutionType;
use App\Modules\Transaction\Domain\ValueObjects\RecurringFrequency;
use Carbon\CarbonImmutable;

final readonly class CreateRecurringTransactionInput
{
    public function __construct(
        public int $userId,
        public int $accountId,
        public ?int $destinationAccountId,
        public ?int $categoryId,
        public string $type,
        public string $amount,
        public string $currency,
        public ?string $description,
        public RecurringFrequency $frequency,
        public ExecutionType $executionType,
        public CarbonImmutable $nextRunDate,
        public bool $isActive = true,
    ) {}
}
