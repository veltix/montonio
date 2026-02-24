<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class UpdateShipmentRequest
{
    /**
     * @param  ShipmentParcel[]|null  $parcels
     */
    public function __construct(
        public ?ShipmentShippingMethod $shippingMethod = null,
        public ?ShipmentReceiver $receiver = null,
        public ?ShipmentSender $sender = null,
        public ?array $parcels = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [];

        if ($this->shippingMethod !== null) {
            $data['shippingMethod'] = $this->shippingMethod->toArray();
        }

        if ($this->receiver !== null) {
            $data['receiver'] = $this->receiver->toArray();
        }

        if ($this->sender !== null) {
            $data['sender'] = $this->sender->toArray();
        }

        if ($this->parcels !== null) {
            $data['parcels'] = array_map(
                fn (ShipmentParcel $parcel) => $parcel->toArray(),
                $this->parcels,
            );
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            shippingMethod: isset($data['shippingMethod']) ? ShipmentShippingMethod::fromArray($data['shippingMethod']) : null,
            receiver: isset($data['receiver']) ? ShipmentReceiver::fromArray($data['receiver']) : null,
            sender: isset($data['sender']) ? ShipmentSender::fromArray($data['sender']) : null,
            parcels: isset($data['parcels']) ? array_map(
                fn (array $parcel) => ShipmentParcel::fromArray($parcel),
                $data['parcels'],
            ) : null,
        );
    }
}
