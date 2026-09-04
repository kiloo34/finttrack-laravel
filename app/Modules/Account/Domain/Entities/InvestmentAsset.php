<?php

namespace App\Modules\Account\Domain\Entities;

final class InvestmentAsset
{
    private function __construct(
        private readonly int $id,
        private readonly int $accountId,
        private string $name,
        private ?string $symbol,
        private string $type,
        private ?int $lots,          // For stocks: number of lots (1 lot = 100 shares in IDX)
        private string $units,       // Shares (lots*100) for stocks; NAB units for mutual funds
        private string $averageBuyPrice,
        private string $currentMarketPrice,
        private ?\DateTimeImmutable $lastSyncedAt,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $accountId,
        string $name,
        string $type,
        string $units = '0.00000000',
        string $averageBuyPrice = '0.00',
        string $currentMarketPrice = '0.00',
        ?string $symbol = null,
        ?int $lots = null,
    ): self {
        $now = new \DateTimeImmutable;

        // For stocks, auto-calculate units from lots
        if ($type === 'stock' && $lots !== null) {
            $units = (string) ($lots * 100);
        }

        return new self(
            id: 0,
            accountId: $accountId,
            name: $name,
            symbol: $symbol,
            type: $type,
            lots: $lots,
            units: $units,
            averageBuyPrice: $averageBuyPrice,
            currentMarketPrice: $currentMarketPrice,
            lastSyncedAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        int $id,
        int $accountId,
        string $name,
        ?string $symbol,
        string $type,
        ?int $lots,
        string $units,
        string $averageBuyPrice,
        string $currentMarketPrice,
        ?\DateTimeImmutable $lastSyncedAt,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            accountId: $accountId,
            name: $name,
            symbol: $symbol,
            type: $type,
            lots: $lots,
            units: $units,
            averageBuyPrice: $averageBuyPrice,
            currentMarketPrice: $currentMarketPrice,
            lastSyncedAt: $lastSyncedAt,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function updateMarketPrice(string $newPrice): void
    {
        $this->currentMarketPrice = $newPrice;
        $this->lastSyncedAt = new \DateTimeImmutable;
        $this->updatedAt = new \DateTimeImmutable;
    }

    /**
     * Calculate total portfolio value for this asset.
     * Stock: lots * 100 shares * price per share
     * Mutual Fund / Others: units * NAB
     */
    public function getPortfolioValue(): string
    {
        /** @var numeric-string $priceStr */
        $priceStr = $this->currentMarketPrice;

        if ($this->type === 'stock' && $this->lots !== null) {
            $shares = bcmul((string) $this->lots, '100', 0);

            /** @var numeric-string $sharesStr */
            $sharesStr = $shares;

            return bcmul($sharesStr, $priceStr, 2);
        }

        /** @var numeric-string $unitsStr */
        $unitsStr = $this->units;

        return bcmul($unitsStr, $priceStr, 2);
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLots(): ?int
    {
        return $this->lots;
    }

    public function getUnits(): string
    {
        return $this->units;
    }

    public function getAverageBuyPrice(): string
    {
        return $this->averageBuyPrice;
    }

    public function getCurrentMarketPrice(): string
    {
        return $this->currentMarketPrice;
    }

    public function getLastSyncedAt(): ?\DateTimeImmutable
    {
        return $this->lastSyncedAt;
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
