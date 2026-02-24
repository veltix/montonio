<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

final readonly class BlikOptions
{
    public function __construct(
        public ?string $preferredLocale = null,
        public ?string $blikCode = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'preferredLocale' => $this->preferredLocale,
            'blikCode' => $this->blikCode,
        ], fn (?string $value) => $value !== null);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            preferredLocale: $data['preferredLocale'] ?? null,
            blikCode: $data['blikCode'] ?? null,
        );
    }
}
