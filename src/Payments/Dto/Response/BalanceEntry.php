<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

use Veltix\Montonio\Support\Decimal;

final readonly class BalanceEntry
{
    public function __construct(
        public string $currency,
        public string $balance,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            currency: $data['currency'],
            balance: Decimal::fromValue($data['balance']),
        );
    }
}
