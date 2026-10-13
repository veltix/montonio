<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

use Veltix\Montonio\Support\Decimal;

final readonly class PaymentIntent
{
    /** @param array<string, mixed>|null $paymentMethodMetadata */
    public function __construct(
        public string $uuid,
        public string $paymentMethodType,
        public ?array $paymentMethodMetadata,
        public string $amount,
        public string $currency,
        public string $status,
        public ?string $serviceFee,
        public ?string $serviceFeeCurrency,
        public string $createdAt,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            paymentMethodType: $data['paymentMethodType'],
            paymentMethodMetadata: $data['paymentMethodMetadata'] ?? null,
            amount: Decimal::fromValue($data['amount']),
            currency: $data['currency'],
            status: $data['status'],
            serviceFee: isset($data['serviceFee']) ? Decimal::fromValue($data['serviceFee']) : null,
            serviceFeeCurrency: $data['serviceFeeCurrency'] ?? null,
            createdAt: $data['createdAt'],
        );
    }
}
