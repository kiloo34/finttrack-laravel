<?php

namespace App\Modules\Budget\Domain\Entities;

use DateTimeImmutable;

/**
 * Pure PHP Domain Entity.
 */
final class Budget
{
    private function __construct(
        private int $id,
        private readonly int $userId,
        private int $categoryId,
        private float $amount,
        private int $month,
        private int $year,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $userId,
        int $categoryId,
        float $amount,
        int $month,
        int $year,
    ): self {
        $now = new DateTimeImmutable;

        return new self(
            id: 0,
            userId: $userId,
            categoryId: $categoryId,
            amount: $amount,
            month: $month,
            year: $year,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        int $id,
        int $userId,
        int $categoryId,
        float $amount,
        int $month,
        int $year,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            categoryId: $categoryId,
            amount: $amount,
            month: $month,
            year: $year,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function update(
        ?int $categoryId = null,
        ?float $amount = null,
        ?int $month = null,
        ?int $year = null,
    ): void {
        if ($categoryId !== null) {
            $this->categoryId = $categoryId;
        }
        if ($amount !== null) {
            $this->amount = $amount;
        }
        if ($month !== null) {
            $this->month = $month;
        }
        if ($year !== null) {
            $this->year = $year;
        }
        $this->updatedAt = new DateTimeImmutable;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function assignId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

