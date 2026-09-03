<?php

namespace App\Modules\Transaction\Domain\Entities;

use App\Modules\Transaction\Domain\Services\HolidayServiceInterface;
use App\Modules\Transaction\Domain\ValueObjects\ExecutionType;
use App\Modules\Transaction\Domain\ValueObjects\RecurringFrequency;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

class RecurringTransaction
{
    private function __construct(
        private int $id,
        private int $userId,
        private int $accountId,
        private ?int $destinationAccountId,
        private ?int $categoryId,
        private string $type,
        private string $amount,
        private string $currency,
        private ?string $description,
        private RecurringFrequency $frequency,
        private ExecutionType $executionType,
        private CarbonImmutable $nextRunDate,
        private bool $isActive,
    ) {
        $this->validate();
    }

    public static function create(
        int $userId,
        int $accountId,
        ?int $destinationAccountId,
        ?int $categoryId,
        string $type,
        string $amount,
        string $currency,
        ?string $description,
        RecurringFrequency $frequency,
        ExecutionType $executionType,
        CarbonImmutable $nextRunDate,
        bool $isActive = true
    ): self {
        return new self(
            id: 0,
            userId: $userId,
            accountId: $accountId,
            destinationAccountId: $destinationAccountId,
            categoryId: $categoryId,
            type: $type,
            amount: $amount,
            currency: $currency,
            description: $description,
            frequency: $frequency,
            executionType: $executionType,
            nextRunDate: $nextRunDate,
            isActive: $isActive
        );
    }

    public static function restore(
        int $id,
        int $userId,
        int $accountId,
        ?int $destinationAccountId,
        ?int $categoryId,
        string $type,
        string $amount,
        string $currency,
        ?string $description,
        string $frequency,
        string $executionType,
        CarbonImmutable $nextRunDate,
        bool $isActive
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            accountId: $accountId,
            destinationAccountId: $destinationAccountId,
            categoryId: $categoryId,
            type: $type,
            amount: $amount,
            currency: $currency,
            description: $description,
            frequency: RecurringFrequency::from($frequency),
            executionType: ExecutionType::from($executionType),
            nextRunDate: $nextRunDate,
            isActive: $isActive
        );
    }

    private function validate(): void
    {
        if ((float) $this->amount <= 0) {
            throw new InvalidArgumentException('Recurring transaction amount must be strictly positive');
        }

        if (! in_array($this->type, ['income', 'expense', 'transfer'], true)) {
            throw new InvalidArgumentException('Invalid transaction type');
        }

        if ($this->type === 'transfer' && ! $this->destinationAccountId) {
            throw new InvalidArgumentException('Transfer must have a destination account');
        }
    }

    public function advanceNextRunDate(?HolidayServiceInterface $holidayService = null): void
    {
        if ($this->frequency === RecurringFrequency::Workdays) {
            do {
                $this->nextRunDate = $this->nextRunDate->addDay();
            } while ($holidayService && ! $holidayService->isWorkday($this->nextRunDate));

            return;
        }

        $this->nextRunDate = match ($this->frequency) {
            RecurringFrequency::Daily => $this->nextRunDate->addDay(),
            RecurringFrequency::Weekly => $this->nextRunDate->addWeek(),
            RecurringFrequency::Monthly => $this->nextRunDate->addMonth(),
            RecurringFrequency::Yearly => $this->nextRunDate->addYear(),
        };
    }

    public function update(
        int $accountId,
        ?int $destinationAccountId,
        ?int $categoryId,
        string $type,
        string $amount,
        string $currency,
        ?string $description,
        RecurringFrequency $frequency,
        ExecutionType $executionType,
        CarbonImmutable $nextRunDate,
        bool $isActive
    ): void {
        $this->accountId = $accountId;
        $this->destinationAccountId = $destinationAccountId;
        $this->categoryId = $categoryId;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->frequency = $frequency;
        $this->executionType = $executionType;
        $this->nextRunDate = $nextRunDate;
        $this->isActive = $isActive;

        $this->validate();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getDestinationAccountId(): ?int
    {
        return $this->destinationAccountId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFrequency(): RecurringFrequency
    {
        return $this->frequency;
    }

    public function getExecutionType(): ExecutionType
    {
        return $this->executionType;
    }

    public function getNextRunDate(): CarbonImmutable
    {
        return $this->nextRunDate;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
