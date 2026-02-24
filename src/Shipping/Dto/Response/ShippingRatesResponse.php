<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShippingRatesResponse
{
    /**
     * @param  CarrierRates[]  $carriers
     */
    public function __construct(
        public RateCalculationDetails $calculationDetails,
        public string $destination,
        public array $carriers,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            calculationDetails: RateCalculationDetails::fromArray($data['calculationDetails']),
            destination: $data['destination'],
            carriers: array_map(
                fn (array $carrier) => CarrierRates::fromArray($carrier),
                $data['carriers'],
            ),
        );
    }
}
