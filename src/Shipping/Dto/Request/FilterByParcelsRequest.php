<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class FilterByParcelsRequest
{
    /**
     * @param  FilterParcel[]  $parcels
     */
    public function __construct(
        public array $parcels,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'parcels' => array_map(
                fn (FilterParcel $parcel) => $parcel->toArray(),
                $this->parcels,
            ),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            parcels: array_map(
                fn (array $parcel) => FilterParcel::fromArray($parcel),
                $data['parcels'],
            ),
        );
    }
}
