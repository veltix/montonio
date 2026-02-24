<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShippingMethodsResponse
{
    /**
     * @param  CountryShippingMethods[]  $countries
     */
    public function __construct(
        public array $countries,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            countries: array_map(
                fn (array $country) => CountryShippingMethods::fromArray($country),
                $data['countries'] ?? $data,
            ),
        );
    }
}
