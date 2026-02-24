<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class StoreInfo
{
    public function __construct(
        public string $uuid,
        public string $name,
        public string $legalName,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            name: $data['name'],
            legalName: $data['legalName'],
        );
    }
}
