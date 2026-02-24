<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

final readonly class PaymentInitiationOptions
{
    public function __construct(
        public ?string $preferredProvider = null,
        public ?string $preferredCountry = null,
        public ?string $preferredLocale = null,
        public ?string $paymentDescription = null,
        public ?string $paymentReference = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'preferredProvider' => $this->preferredProvider,
            'preferredCountry' => $this->preferredCountry,
            'preferredLocale' => $this->preferredLocale,
            'paymentDescription' => $this->paymentDescription,
            'paymentReference' => $this->paymentReference,
        ], fn (?string $value) => $value !== null);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            preferredProvider: $data['preferredProvider'] ?? null,
            preferredCountry: $data['preferredCountry'] ?? null,
            preferredLocale: $data['preferredLocale'] ?? null,
            paymentDescription: $data['paymentDescription'] ?? null,
            paymentReference: $data['paymentReference'] ?? null,
        );
    }
}
