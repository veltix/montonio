<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class WebhookResponse
{
    /**
     * @param  string[]  $enabledEvents
     */
    public function __construct(
        public string $id,
        public string $createdAt,
        public string $url,
        public array $enabledEvents,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            createdAt: $data['createdAt'],
            url: $data['url'],
            enabledEvents: $data['enabledEvents'],
        );
    }
}
