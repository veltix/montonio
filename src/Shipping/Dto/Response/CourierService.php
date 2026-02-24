<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CourierService
{
    /**
     * @param  PickupPointAdditionalService[]  $additionalServices
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $type,
        public string $carrierCode,
        public array $additionalServices,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            type: $data['type'],
            carrierCode: $data['carrierCode'],
            additionalServices: array_map(
                fn (array $service) => PickupPointAdditionalService::fromArray($service),
                $data['additionalServices'] ?? [],
            ),
        );
    }
}
