<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Support\Decimal;

final readonly class CodParams
{
    public function __construct(
        public string $amount,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'amount' => Decimal::toNumber($this->amount),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: Decimal::fromValue($data['amount']),
        );
    }
}
