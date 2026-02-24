<?php

declare(strict_types=1);

namespace Veltix\Montonio\Webhook\Dto;

use Veltix\Montonio\Payments\Enum\PaymentStatus;

final readonly class PaymentWebhookPayload
{
    public function __construct(
        public string $uuid,
        public string $accessKey,
        public string $merchantReference,
        public ?string $merchantReferenceDisplay,
        public PaymentStatus $paymentStatus,
        public ?string $paymentMethod,
        public float $grandTotal,
        public string $currency,
        public ?string $senderIban,
        public ?string $senderName,
        public ?string $paymentProviderName,
        public ?string $paymentLinkUuid,
        public int $iat,
        public int $exp,
    ) {}

    /** @param object{uuid: string, accessKey: string, merchantReference: string, merchantReferenceDisplay?: string, paymentStatus: string, paymentMethod?: string, grandTotal: float|int|string, currency: string, senderIban?: string, senderName?: string, paymentProviderName?: string, paymentLinkUuid?: string, iat: int, exp: int} $data */
    public static function fromObject(object $data): self
    {
        return new self(
            uuid: $data->uuid,
            accessKey: $data->accessKey,
            merchantReference: $data->merchantReference,
            merchantReferenceDisplay: $data->merchantReferenceDisplay ?? null,
            paymentStatus: PaymentStatus::from($data->paymentStatus),
            paymentMethod: $data->paymentMethod ?? null,
            grandTotal: (float) $data->grandTotal,
            currency: $data->currency,
            senderIban: $data->senderIban ?? null,
            senderName: $data->senderName ?? null,
            paymentProviderName: $data->paymentProviderName ?? null,
            paymentLinkUuid: $data->paymentLinkUuid ?? null,
            iat: (int) $data->iat,
            exp: (int) $data->exp,
        );
    }
}
