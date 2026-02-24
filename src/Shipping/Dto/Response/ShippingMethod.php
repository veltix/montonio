<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShippingMethod
{
    /**
     * @param  ShippingMethodSubtype[]|null  $subtypes
     */
    public function __construct(
        public string $type,
        public ?array $subtypes = null,
        public ?ShippingMethodConstraints $constraints = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            subtypes: isset($data['subtypes']) ? array_map(
                fn (array $subtype) => ShippingMethodSubtype::fromArray($subtype),
                $data['subtypes'],
            ) : null,
            constraints: isset($data['constraints']) ? ShippingMethodConstraints::fromArray($data['constraints']) : null,
        );
    }
}
