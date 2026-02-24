<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;

final readonly class CreatePaymentLinkRequest
{
    public function __construct(
        public string $description,
        public Currency $currency,
        public float $amount,
        public Locale $locale,
        public bool $askAdditionalInfo,
        public string $expiresAt,
        public ?string $type = null,
        public ?string $notificationUrl = null,
        public ?string $returnUrl = null,
        public ?string $preferredProvider = null,
        public ?string $preferredCountry = null,
        public ?string $merchantReference = null,
        public ?string $paymentReference = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'description' => $this->description,
            'currency' => $this->currency->value,
            'amount' => $this->amount,
            'locale' => $this->locale->value,
            'askAdditionalInfo' => $this->askAdditionalInfo,
            'expiresAt' => $this->expiresAt,
        ];

        if ($this->type !== null) {
            $data['type'] = $this->type;
        }

        if ($this->notificationUrl !== null) {
            $data['notificationUrl'] = $this->notificationUrl;
        }

        if ($this->returnUrl !== null) {
            $data['returnUrl'] = $this->returnUrl;
        }

        if ($this->preferredProvider !== null) {
            $data['preferredProvider'] = $this->preferredProvider;
        }

        if ($this->preferredCountry !== null) {
            $data['preferredCountry'] = $this->preferredCountry;
        }

        if ($this->merchantReference !== null) {
            $data['merchantReference'] = $this->merchantReference;
        }

        if ($this->paymentReference !== null) {
            $data['paymentReference'] = $this->paymentReference;
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'],
            currency: Currency::from($data['currency']),
            amount: (float) $data['amount'],
            locale: Locale::from($data['locale']),
            askAdditionalInfo: (bool) $data['askAdditionalInfo'],
            expiresAt: $data['expiresAt'],
            type: $data['type'] ?? null,
            notificationUrl: $data['notificationUrl'] ?? null,
            returnUrl: $data['returnUrl'] ?? null,
            preferredProvider: $data['preferredProvider'] ?? null,
            preferredCountry: $data['preferredCountry'] ?? null,
            merchantReference: $data['merchantReference'] ?? null,
            paymentReference: $data['paymentReference'] ?? null,
        );
    }
}
