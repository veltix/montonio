<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class CreateShipmentRequest
{
    /**
     * @param  ShipmentParcel[]  $parcels
     * @param  ShipmentProduct[]|null  $products
     */
    public function __construct(
        public ShipmentShippingMethod $shippingMethod,
        public ShipmentReceiver $receiver,
        public array $parcels,
        public ?ShipmentSender $sender = null,
        public ?string $merchantReference = null,
        public ?string $montonioOrderUuid = null,
        public ?string $orderComment = null,
        public ?array $products = null,
        public ?bool $synchronous = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'shippingMethod' => $this->shippingMethod->toArray(),
            'receiver' => $this->receiver->toArray(),
            'parcels' => array_map(
                fn (ShipmentParcel $parcel) => $parcel->toArray(),
                $this->parcels,
            ),
        ];

        if ($this->sender !== null) {
            $data['sender'] = $this->sender->toArray();
        }

        if ($this->merchantReference !== null) {
            $data['merchantReference'] = $this->merchantReference;
        }

        if ($this->montonioOrderUuid !== null) {
            $data['montonioOrderUuid'] = $this->montonioOrderUuid;
        }

        if ($this->orderComment !== null) {
            $data['orderComment'] = $this->orderComment;
        }

        if ($this->products !== null) {
            $data['products'] = array_map(
                fn (ShipmentProduct $product) => $product->toArray(),
                $this->products,
            );
        }

        if ($this->synchronous !== null) {
            $data['synchronous'] = $this->synchronous;
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            shippingMethod: ShipmentShippingMethod::fromArray($data['shippingMethod']),
            receiver: ShipmentReceiver::fromArray($data['receiver']),
            parcels: array_map(
                fn (array $parcel) => ShipmentParcel::fromArray($parcel),
                $data['parcels'],
            ),
            sender: isset($data['sender']) ? ShipmentSender::fromArray($data['sender']) : null,
            merchantReference: $data['merchantReference'] ?? null,
            montonioOrderUuid: $data['montonioOrderUuid'] ?? null,
            orderComment: $data['orderComment'] ?? null,
            products: isset($data['products']) ? array_map(
                fn (array $product) => ShipmentProduct::fromArray($product),
                $data['products'],
            ) : null,
            synchronous: $data['synchronous'] ?? null,
        );
    }
}
