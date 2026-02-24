<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

final readonly class CardPaymentOptions
{
    public function __construct(
        public ?string $preferredMethod = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'preferredMethod' => $this->preferredMethod,
        ], fn (?string $value) => $value !== null);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            preferredMethod: $data['preferredMethod'] ?? null,
        );
    }
}
