<?php

namespace App\Modules\Category\Domain\Entities;

use App\Modules\Category\Domain\Exceptions\SystemCategoryModificationException;

/**
 * Pure PHP Domain Entity.
 */
final class Category
{
    private function __construct(
        private readonly int $id,
        private readonly ?int $userId,
        private ?int $parentId,
        private string $name,
        private string $type,
        private readonly bool $isSystem,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $userId,
        string $name,
        string $type,
        ?int $parentId = null,
    ): self {
        $now = new \DateTimeImmutable;

        return new self(
            id: 0, // assigned by DB on persist
            userId: $userId,
            parentId: $parentId,
            name: $name,
            type: $type,
            isSystem: false,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        int $id,
        ?int $userId,
        ?int $parentId,
        string $name,
        string $type,
        bool $isSystem,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            parentId: $parentId,
            name: $name,
            type: $type,
            isSystem: $isSystem,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function update(string $name, string $type, ?int $parentId = null): void
    {
        if ($this->isSystem) {
            throw new SystemCategoryModificationException('System categories cannot be modified.');
        }
        $this->name = $name;
        $this->type = $type;
        $this->parentId = $parentId;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function ensureCanBeDeleted(): void
    {
        if ($this->isSystem) {
            throw new SystemCategoryModificationException('System categories cannot be deleted.');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
