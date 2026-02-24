<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class EstimatedParcel
{
    public function __construct(
        public float $length,
        public float $width,
        public float $height,
        public string $dimensionUnit,
        public float $actualWeight,
        public float $volumetricWeight,
        public float $chargeableWeight,
        public string $weightUnit,
        public bool $bufferApplied,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            length: (float) $data['length'],
            width: (float) $data['width'],
            height: (float) $data['height'],
            dimensionUnit: $data['dimensionUnit'],
            actualWeight: (float) $data['actualWeight'],
            volumetricWeight: (float) $data['volumetricWeight'],
            chargeableWeight: (float) $data['chargeableWeight'],
            weightUnit: $data['weightUnit'],
            bufferApplied: (bool) $data['bufferApplied'],
        );
    }
}
