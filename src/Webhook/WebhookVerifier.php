<?php

declare(strict_types=1);

namespace Veltix\Montonio\Webhook;

use Veltix\Montonio\Auth\JwtDecoder;
use Veltix\Montonio\Webhook\Dto\PaymentWebhookPayload;
use Veltix\Montonio\Webhook\Dto\ShippingWebhookPayload;

final readonly class WebhookVerifier
{
    public function __construct(
        private JwtDecoder $jwtDecoder,
    ) {}

    public function verifyPaymentWebhook(string $orderToken): PaymentWebhookPayload
    {
        /** @var object{uuid: string, accessKey: string, merchantReference: string, merchantReferenceDisplay?: string, paymentStatus: string, paymentMethod?: string, grandTotal: float|int|string, currency: string, senderIban?: string, senderName?: string, paymentProviderName?: string, paymentLinkUuid?: string, iat: int, exp: int} $decoded */
        $decoded = $this->jwtDecoder->decode($orderToken);

        return PaymentWebhookPayload::fromObject($decoded);
    }

    public function verifyShippingWebhook(string $payload): ShippingWebhookPayload
    {
        /** @var object{eventId: string, shipmentId?: string, created: string, data: object, eventType: string, iat: int, exp: int} $decoded */
        $decoded = $this->jwtDecoder->decode($payload);

        return ShippingWebhookPayload::fromObject($decoded);
    }
}
