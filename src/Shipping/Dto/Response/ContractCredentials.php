<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ContractCredentials
{
    public function __construct(
        public ?string $username = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'] ?? null,
        );
    }
}
