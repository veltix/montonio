<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

use Veltix\Montonio\Support\Decimal;

final readonly class ShipmentProductResponse
{
    /** @param array<string, mixed>|null $attributes */
    public function __construct(
        public string $sku,
        public string $name,
        public int|string $quantity,
        public ?string $barcode = null,
        public ?string $price = null,
        public ?string $currency = null,
        public ?array $attributes = null,
        public ?string $imageUrl = null,
        public ?string $storeProductUrl = null,
        public ?string $description = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'],
            name: $data['name'],
            quantity: is_int($data['quantity']) ? $data['quantity'] : Decimal::fromValue($data['quantity']),
            barcode: $data['barcode'] ?? null,
            price: isset($data['price']) ? Decimal::fromValue($data['price']) : null,
            currency: $data['currency'] ?? null,
            attributes: $data['attributes'] ?? null,
            imageUrl: $data['imageUrl'] ?? null,
            storeProductUrl: $data['storeProductUrl'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
