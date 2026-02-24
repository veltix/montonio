<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class RateCalculationDetails
{
    /**
     * @param  EstimatedParcel[]  $estimatedParcels
     */
    public function __construct(
        public array $estimatedParcels,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            estimatedParcels: array_map(
                fn (array $parcel) => EstimatedParcel::fromArray($parcel),
                $data['estimatedParcels'] ?? [],
            ),
        );
    }
}
