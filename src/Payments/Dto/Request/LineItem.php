<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

use Veltix\Montonio\Support\Decimal;

final readonly class LineItem
{
    public function __construct(
        public string $name,
        public int $quantity,
        public string $finalPrice,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'finalPrice' => Decimal::toNumber($this->finalPrice),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            quantity: $data['quantity'],
            finalPrice: Decimal::fromValue($data['finalPrice']),
        );
    }
}
