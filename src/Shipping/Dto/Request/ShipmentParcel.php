<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class ShipmentParcel
{
    public function __construct(
        public float $weight,
        public ?float $height = null,
        public ?float $width = null,
        public ?float $length = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'weight' => $this->weight,
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ], fn (?float $value) => $value !== null);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            weight: (float) $data['weight'],
            height: isset($data['height']) ? (float) $data['height'] : null,
            width: isset($data['width']) ? (float) $data['width'] : null,
            length: isset($data['length']) ? (float) $data['length'] : null,
        );
    }
}
