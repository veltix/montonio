<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

use Veltix\Montonio\Support\Decimal;

final readonly class RateSubtype
{
    public function __construct(
        public string $code,
        public string $rate,
        public string $currency,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            rate: Decimal::fromValue($data['rate']),
            currency: $data['currency'],
        );
    }
}
