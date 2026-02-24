<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class CodParams
{
    public function __construct(
        public float $amount,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
        );
    }
}
