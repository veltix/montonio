<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShipmentSenderResponse
{
    public function __construct(
        public string $name,
        public string $phoneCountryCode,
        public string $phoneNumber,
        public ?string $streetAddress = null,
        public ?string $locality = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $region = null,
        public ?string $email = null,
        public ?string $companyName = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            phoneCountryCode: $data['phoneCountryCode'],
            phoneNumber: $data['phoneNumber'],
            streetAddress: $data['streetAddress'] ?? null,
            locality: $data['locality'] ?? null,
            postalCode: $data['postalCode'] ?? null,
            country: $data['country'] ?? null,
            region: $data['region'] ?? null,
            email: $data['email'] ?? null,
            companyName: $data['companyName'] ?? null,
        );
    }
}
