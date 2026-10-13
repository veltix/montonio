<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

use Veltix\Montonio\Support\Decimal;

final readonly class ShipmentParcelResponse
{
    public function __construct(
        public string $weight,
        public ?string $height = null,
        public ?string $width = null,
        public ?string $length = null,
    ) {}

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
