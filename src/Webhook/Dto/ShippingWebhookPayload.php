<?php

declare(strict_types=1);

namespace Veltix\Montonio\Webhook\Dto;

use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;

final readonly class ShippingWebhookPayload
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $eventId,
        public ?string $shipmentId,
        public string $created,
        public array $data,
        public ShippingWebhookEvent $eventType,
        public int $iat,
        public int $exp,
    ) {}

    /** @param object{eventId: string, shipmentId?: string, created: string, data: object, eventType: string, iat: int, exp: int} $data */
    public static function fromObject(object $data): self
    {
        return new self(
            eventId: $data->eventId,
            shipmentId: $data->shipmentId ?? null,
            created: $data->created,
            data: (array) $data->data,
            eventType: ShippingWebhookEvent::from($data->eventType),
            iat: (int) $data->iat,
            exp: (int) $data->exp,
        );
    }
}
