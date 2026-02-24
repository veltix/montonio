<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CourierServicesResponse
{
    /**
     * @param  CourierService[]  $courierServices
     */
    public function __construct(
        public array $courierServices,
        public string $countryCode,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            courierServices: array_map(
                fn (array $service) => CourierService::fromArray($service),
                $data['courierServices'],
            ),
            countryCode: $data['countryCode'],
        );
    }
}
