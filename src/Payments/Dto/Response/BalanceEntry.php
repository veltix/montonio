<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class BalanceEntry
{
    public function __construct(
        public string $currency,
        public float $balance,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            currency: $data['currency'],
            balance: (float) $data['balance'],
        );
    }
}
