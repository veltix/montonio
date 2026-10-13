<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

use Veltix\Montonio\Support\Decimal;

final readonly class EstimatedParcel
{
    public function __construct(
        public string $length,
        public string $width,
        public string $height,
        public string $dimensionUnit,
        public string $actualWeight,
        public string $volumetricWeight,
        public string $chargeableWeight,
        public string $weightUnit,
        public bool $bufferApplied,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            length: Decimal::fromValue($data['length']),
            width: Decimal::fromValue($data['width']),
            height: Decimal::fromValue($data['height']),
            dimensionUnit: $data['dimensionUnit'],
            actualWeight: Decimal::fromValue($data['actualWeight']),
            volumetricWeight: Decimal::fromValue($data['volumetricWeight']),
            chargeableWeight: Decimal::fromValue($data['chargeableWeight']),
            weightUnit: $data['weightUnit'],
            bufferApplied: (bool) $data['bufferApplied'],
        );
    }
}
