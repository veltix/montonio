<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class StoreBalancesResponse
{
    /**
     * @param  array<string, BalanceEntry[]>  $balances
     */
    public function __construct(
        public StoreInfo $store,
        public array $balances,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $balances = [];
        foreach ($data['balances'] as $key => $entries) {
            $balances[$key] = array_map(
                fn (array $entry) => BalanceEntry::fromArray($entry),
                $entries,
            );
        }

        return new self(
            store: StoreInfo::fromArray($data['store']),
            balances: $balances,
        );
    }
}
