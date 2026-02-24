<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class PaymentLinkResponse
{
    public function __construct(
        public string $uuid,
        public string $url,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            url: $data['url'],
        );
    }
}
