<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class CountryPaymentMethods
{
    /**
     * @param  string[]  $supportedCurrencies
     * @param  BankPaymentMethod[]  $paymentMethods
     */
    public function __construct(
        public array $supportedCurrencies,
        public array $paymentMethods,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            supportedCurrencies: $data['supportedCurrencies'],
            paymentMethods: array_map(
                fn (array $method) => BankPaymentMethod::fromArray($method),
                $data['paymentMethods'],
            ),
        );
    }
}
