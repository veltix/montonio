<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CarriersResponse
{
    /**
     * @param  Carrier[]  $carriers
     */
    public function __construct(
        public array $carriers,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            carriers: array_map(
                fn (array $carrier) => Carrier::fromArray($carrier),
                $data['carriers'] ?? $data,
            ),
        );
    }
}
