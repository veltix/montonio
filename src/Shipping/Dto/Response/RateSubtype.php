<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class RateSubtype
{
    public function __construct(
        public string $code,
        public float $rate,
        public string $currency,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            rate: (float) $data['rate'],
            currency: $data['currency'],
        );
    }
}
