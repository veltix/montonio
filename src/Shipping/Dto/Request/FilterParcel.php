<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Support\Decimal;

final readonly class FilterParcel
{
    public function __construct(
        public string $weight,
        public ?string $height = null,
        public ?string $width = null,
        public ?string $length = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_map(Decimal::toNumber(...), array_filter([
            'weight' => $this->weight,
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ], fn (?string $value) => $value !== null));
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            weight: Decimal::fromValue($data['weight']),
            height: isset($data['height']) ? Decimal::fromValue($data['height']) : null,
            width: isset($data['width']) ? Decimal::fromValue($data['width']) : null,
            length: isset($data['length']) ? Decimal::fromValue($data['length']) : null,
        );
    }
}
