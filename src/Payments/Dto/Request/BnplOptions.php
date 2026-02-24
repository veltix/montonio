<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

final readonly class BnplOptions
{
    public function __construct(
        public ?int $period = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'period' => $this->period,
        ], fn (?int $value) => $value !== null);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            period: isset($data['period']) ? (int) $data['period'] : null,
        );
    }
}
