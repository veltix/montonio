<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShippingMethodConstraints
{
    public function __construct(
        public bool $parcelDimensionsRequired,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            parcelDimensionsRequired: (bool) $data['parcelDimensionsRequired'],
        );
    }
}
