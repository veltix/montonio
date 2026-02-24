<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Shipping\Enum\DimensionUnit;
use Veltix\Montonio\Shipping\Enum\WeightUnit;

final readonly class RatesItem
{
    public function __construct(
        public float $length,
        public float $width,
        public float $height,
        public float $weight,
        public ?DimensionUnit $dimensionUnit = null,
        public ?WeightUnit $weightUnit = null,
        public ?int $quantity = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'weight' => $this->weight,
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
            length: (float) $data['length'],
            width: (float) $data['width'],
            height: (float) $data['height'],
            weight: (float) $data['weight'],
            dimensionUnit: isset($data['dimensionUnit']) ? DimensionUnit::from($data['dimensionUnit']) : null,
            weightUnit: isset($data['weightUnit']) ? WeightUnit::from($data['weightUnit']) : null,
            quantity: $data['quantity'] ?? null,
        );
    }
}
