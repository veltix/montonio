<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\PaymentMethodCode;

final readonly class Payment
{
    public function __construct(
        public float $amount,
        public Currency $currency,
        public PaymentMethodCode $method,
        public ?string $methodDisplay = null,
        public PaymentInitiationOptions|CardPaymentOptions|BlikOptions|BnplOptions|null $methodOptions = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'method' => $this->method->value,
        ];

        if ($this->methodDisplay !== null) {
            $data['methodDisplay'] = $this->methodDisplay;
        }

        if ($this->methodOptions !== null) {
            $data['methodOptions'] = $this->methodOptions->toArray();
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $methodOptions = null;
        if (isset($data['methodOptions'])) {
            $method = PaymentMethodCode::from($data['method']);
            $methodOptions = match ($method) {
                PaymentMethodCode::PaymentInitiation => PaymentInitiationOptions::fromArray($data['methodOptions']),
                PaymentMethodCode::CardPayments => CardPaymentOptions::fromArray($data['methodOptions']),
                PaymentMethodCode::Blik => BlikOptions::fromArray($data['methodOptions']),
                PaymentMethodCode::Bnpl, PaymentMethodCode::HirePurchase => BnplOptions::fromArray($data['methodOptions']),
            };
        }

        return new self(
            amount: (float) $data['amount'],
            currency: Currency::from($data['currency']),
            method: PaymentMethodCode::from($data['method']),
            methodDisplay: $data['methodDisplay'] ?? null,
            methodOptions: $methodOptions,
        );
    }
}
