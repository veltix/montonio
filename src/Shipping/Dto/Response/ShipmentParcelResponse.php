<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class ShipmentParcelResponse
{
    public function __construct(
        public float $weight,
        public ?float $height = null,
        public ?float $width = null,
        public ?float $length = null,
    ) {}

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
