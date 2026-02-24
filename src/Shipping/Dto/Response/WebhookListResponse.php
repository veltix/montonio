<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class WebhookListResponse
{
    /**
     * @param  WebhookResponse[]  $data
     */
    public function __construct(
        public array $data,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(
                fn (array $webhook) => WebhookResponse::fromArray($webhook),
                $data['data'] ?? $data,
            ),
        );
    }
}
