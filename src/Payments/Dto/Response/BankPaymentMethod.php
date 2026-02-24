<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class BankPaymentMethod
{
    /**
     * @param  string[]  $supportedCurrencies
     */
    public function __construct(
        public string $code,
        public string $name,
        public string $logoUrl,
        public array $supportedCurrencies,
        public ?int $uiPosition = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            logoUrl: $data['logoUrl'],
            supportedCurrencies: $data['supportedCurrencies'],
            uiPosition: $data['uiPosition'] ?? null,
        );
    }
}
