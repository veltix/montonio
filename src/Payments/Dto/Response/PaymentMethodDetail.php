<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class PaymentMethodDetail
{
    /**
     * @param  array<string, CountryPaymentMethods>|null  $setup
     */
    public function __construct(
        public string $processor,
        public ?string $logoUrl = null,
        public ?array $setup = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $setup = null;
        if (isset($data['setup'])) {
            $setup = [];
            foreach ($data['setup'] as $countryCode => $countryData) {
                $setup[$countryCode] = CountryPaymentMethods::fromArray($countryData);
            }
        }

        return new self(
            processor: $data['processor'],
            logoUrl: $data['logoUrl'] ?? null,
            setup: $setup,
        );
    }
}
