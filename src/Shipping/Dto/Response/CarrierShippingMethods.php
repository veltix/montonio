<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CarrierShippingMethods
{
    /**
     * @param  ShippingMethod[]  $shippingMethods
     */
    public function __construct(
        public string $carrierCode,
        public array $shippingMethods,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            carrierCode: $data['carrierCode'],
            shippingMethods: array_map(
                fn (array $method) => ShippingMethod::fromArray($method),
                $data['shippingMethods'],
            ),
        );
    }
}
