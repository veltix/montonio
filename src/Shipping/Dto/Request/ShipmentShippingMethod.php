<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Shipping\Enum\LockerSize;
use Veltix\Montonio\Shipping\Enum\ParcelHandoverMethod;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;

final readonly class ShipmentShippingMethod
{
    /**
     * @param  AdditionalService[]|null  $additionalServices
     */
    public function __construct(
        public ShippingMethodType $type,
        public string $id,
        public ?array $additionalServices = null,
        public ?ParcelHandoverMethod $parcelHandoverMethod = null,
        public ?LockerSize $lockerSize = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type->value,
            'id' => $this->id,
        ];

        if ($this->additionalServices !== null) {
            $data['additionalServices'] = array_map(
                fn (AdditionalService $service) => $service->toArray(),
                $this->additionalServices,
            );
        }

        if ($this->parcelHandoverMethod !== null) {
            $data['parcelHandoverMethod'] = $this->parcelHandoverMethod->value;
        }

        if ($this->lockerSize !== null) {
            $data['lockerSize'] = $this->lockerSize->value;
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            type: ShippingMethodType::from($data['type']),
            id: $data['id'],
            additionalServices: isset($data['additionalServices']) ? array_map(
                fn (array $service) => AdditionalService::fromArray($service),
                $data['additionalServices'],
            ) : null,
            parcelHandoverMethod: isset($data['parcelHandoverMethod']) ? ParcelHandoverMethod::from($data['parcelHandoverMethod']) : null,
            lockerSize: isset($data['lockerSize']) ? LockerSize::from($data['lockerSize']) : null,
        );
    }
}
