<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class PaymentMethodsResponse
{
    /**
     * @param  array<string, PaymentMethodDetail>  $paymentMethods
     */
    public function __construct(
        public string $uuid,
        public string $name,
        public array $paymentMethods,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $paymentMethods = [];
        foreach ($data['paymentMethods'] as $key => $detail) {
            $paymentMethods[$key] = PaymentMethodDetail::fromArray($detail);
        }

        return new self(
            uuid: $data['uuid'],
            name: $data['name'],
            paymentMethods: $paymentMethods,
        );
    }
}
