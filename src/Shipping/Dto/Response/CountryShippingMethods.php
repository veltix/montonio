<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CountryShippingMethods
{
    /**
     * @param  CarrierShippingMethods[]  $carriers
     */
    public function __construct(
        public string $countryCode,
        public array $carriers,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            countryCode: $data['countryCode'],
            carriers: array_map(
                fn (array $carrier) => CarrierShippingMethods::fromArray($carrier),
                $data['carriers'],
            ),
        );
    }
}
