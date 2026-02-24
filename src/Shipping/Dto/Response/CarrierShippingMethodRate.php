<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CarrierShippingMethodRate
{
    /**
     * @param  RateSubtype[]  $subtypes
     */
    public function __construct(
        public string $type,
        public array $subtypes,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            subtypes: array_map(
                fn (array $subtype) => RateSubtype::fromArray($subtype),
                $data['subtypes'] ?? [],
            ),
        );
    }
}
