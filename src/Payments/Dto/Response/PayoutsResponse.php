<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class PayoutsResponse
{
    /**
     * @param  Payout[]  $payouts
     */
    public function __construct(
        public array $payouts,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            payouts: array_map(
                fn (array $payout) => Payout::fromArray($payout),
                $data['payouts'] ?? $data,
            ),
        );
    }
}
