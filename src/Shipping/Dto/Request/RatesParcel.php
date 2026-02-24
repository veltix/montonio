<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

final readonly class RatesParcel
{
    /**
     * @param  RatesItem[]  $items
     */
    public function __construct(
        public array $items,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'items' => array_map(
                fn (RatesItem $item) => $item->toArray(),
                $this->items,
            ),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            items: array_map(
                fn (array $item) => RatesItem::fromArray($item),
                $data['items'],
            ),
        );
    }
}
