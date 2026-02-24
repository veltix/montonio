<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class ShippingRatesRequest
{
    /**
     * @param  RatesParcel[]  $parcels
     */
    public function __construct(
        public string $destination,
        public array $parcels,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'destination' => $this->destination,
            'parcels' => array_map(
                fn (RatesParcel $parcel) => $parcel->toArray(),
                $this->parcels,
            ),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            destination: $data['destination'],
            parcels: array_map(
                fn (array $parcel) => RatesParcel::fromArray($parcel),
                $data['parcels'],
            ),
        );
    }
}
