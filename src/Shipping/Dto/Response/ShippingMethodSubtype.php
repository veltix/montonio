<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShippingMethodSubtype
{
    public function __construct(
        public string $code,
        public ?float $rate = null,
        public ?string $currency = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            rate: isset($data['rate']) ? (float) $data['rate'] : null,
            currency: $data['currency'] ?? null,
        );
    }
}
