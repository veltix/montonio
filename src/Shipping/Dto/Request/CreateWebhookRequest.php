<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;

final readonly class CreateWebhookRequest
{
    /**
     * @param  ShippingWebhookEvent[]  $enabledEvents
     */
    public function __construct(
        public string $url,
        public array $enabledEvents,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'enabledEvents' => array_map(
                fn (ShippingWebhookEvent $event) => $event->value,
                $this->enabledEvents,
            ),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'],
            enabledEvents: array_map(
                fn (string $event) => ShippingWebhookEvent::from($event),
                $data['enabledEvents'],
            ),
        );
    }
}
