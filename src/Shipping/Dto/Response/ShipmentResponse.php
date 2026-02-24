<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

use Veltix\Montonio\Shipping\Enum\ShipmentStatus;

final readonly class ShipmentResponse
{
    /**
     * @param  ShipmentParcelResponse[]  $parcels
     * @param  ShipmentProductResponse[]|null  $products
     */
    public function __construct(
        public string $id,
        public string $createdAt,
        public ShipmentStatus $status,
        public ?string $montonioOrderUuid,
        public ?string $merchantReference,
        public ShipmentSenderResponse $sender,
        public ShipmentReceiverResponse $receiver,
        public array $parcels,
        public ShipmentShippingMethodResponse $shippingMethod,
        public ?string $carrierShipmentId,
        public ShipmentStoreResponse $store,
        public ?array $products = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            createdAt: $data['createdAt'],
            status: ShipmentStatus::from($data['status']),
            montonioOrderUuid: $data['montonioOrderUuid'] ?? null,
            merchantReference: $data['merchantReference'] ?? null,
            sender: ShipmentSenderResponse::fromArray($data['sender']),
            receiver: ShipmentReceiverResponse::fromArray($data['receiver']),
            parcels: array_map(
                fn (array $parcel) => ShipmentParcelResponse::fromArray($parcel),
                $data['parcels'],
            ),
            shippingMethod: ShipmentShippingMethodResponse::fromArray($data['shippingMethod']),
            carrierShipmentId: $data['carrierShipmentId'] ?? null,
            store: ShipmentStoreResponse::fromArray($data['store']),
            products: isset($data['products']) ? array_map(
                fn (array $product) => ShipmentProductResponse::fromArray($product),
                $data['products'],
            ) : null,
        );
    }
}
