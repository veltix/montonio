<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

final readonly class Address
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $phoneCountry = null,
        public ?string $addressLine1 = null,
        public ?string $addressLine2 = null,
        public ?string $locality = null,
        public ?string $region = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $companyName = null,
        public ?string $companyLegalName = null,
        public ?string $companyRegCode = null,
        public ?string $companyVatNumber = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'phoneCountry' => $this->phoneCountry,
            'addressLine1' => $this->addressLine1,
            'addressLine2' => $this->addressLine2,
            'locality' => $this->locality,
            'region' => $this->region,
            'country' => $this->country,
            'postalCode' => $this->postalCode,
            'companyName' => $this->companyName,
            'companyLegalName' => $this->companyLegalName,
            'companyRegCode' => $this->companyRegCode,
            'companyVatNumber' => $this->companyVatNumber,
        ], fn (?string $value) => $value !== null);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'] ?? null,
            lastName: $data['lastName'] ?? null,
            email: $data['email'] ?? null,
            phoneNumber: $data['phoneNumber'] ?? null,
            phoneCountry: $data['phoneCountry'] ?? null,
            addressLine1: $data['addressLine1'] ?? null,
            addressLine2: $data['addressLine2'] ?? null,
            locality: $data['locality'] ?? null,
            region: $data['region'] ?? null,
            country: $data['country'] ?? null,
            postalCode: $data['postalCode'] ?? null,
            companyName: $data['companyName'] ?? null,
            companyLegalName: $data['companyLegalName'] ?? null,
            companyRegCode: $data['companyRegCode'] ?? null,
            companyVatNumber: $data['companyVatNumber'] ?? null,
        );
    }
}
