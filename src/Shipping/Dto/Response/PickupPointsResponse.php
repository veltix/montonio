<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class PickupPointsResponse
{
    /**
     * @param  PickupPoint[]  $pickupPoints
     */
    public function __construct(
        public array $pickupPoints,
        public string $countryCode,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            pickupPoints: array_map(
                fn (array $point) => PickupPoint::fromArray($point),
                $data['pickupPoints'],
            ),
            countryCode: $data['countryCode'],
        );
    }
}
