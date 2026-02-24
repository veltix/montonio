<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShipmentShippingMethodResponse
{
    public function __construct(
        public string $type,
        public string $id,
        public ?string $parcelHandoverMethod = null,
        public ?string $lockerSize = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            id: $data['id'],
            parcelHandoverMethod: $data['parcelHandoverMethod'] ?? null,
            lockerSize: $data['lockerSize'] ?? null,
        );
    }
}
