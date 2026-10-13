<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Shipping\Enum\DimensionUnit;
use Veltix\Montonio\Shipping\Enum\WeightUnit;
use Veltix\Montonio\Support\Decimal;

final readonly class RatesItem
{
    public function __construct(
        public string $length,
        public string $width,
        public string $height,
        public string $weight,
        public ?DimensionUnit $dimensionUnit = null,
        public ?WeightUnit $weightUnit = null,
        public ?int $quantity = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'length' => Decimal::toNumber($this->length),
            'width' => Decimal::toNumber($this->width),
            'height' => Decimal::toNumber($this->height),
            'weight' => Decimal::toNumber($this->weight),
        ];

        if ($this->dimensionUnit !== null) {
            $data['dimensionUnit'] = $this->dimensionUnit->value;
        }

        if ($this->weightUnit !== null) {
            $data['weightUnit'] = $this->weightUnit->value;
        }

        if ($this->quantity !== null) {
            $data['quantity'] = $this->quantity;
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            length: Decimal::fromValue($data['length']),
            width: Decimal::fromValue($data['width']),
            height: Decimal::fromValue($data['height']),
            weight: Decimal::fromValue($data['weight']),
            dimensionUnit: isset($data['dimensionUnit']) ? DimensionUnit::from($data['dimensionUnit']) : null,
            weightUnit: isset($data['weightUnit']) ? WeightUnit::from($data['weightUnit']) : null,
            quantity: $data['quantity'] ?? null,
        );
    }
}
