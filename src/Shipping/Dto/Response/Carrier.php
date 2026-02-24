<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class Carrier
{
    /**
     * @param  CarrierContract[]|null  $contracts
     * @param  string[]  $supportedContractTypes
     */
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public string $logoUrl,
        public ?array $contracts,
        public bool $hasMontonioContract,
        public array $supportedContractTypes,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            code: $data['code'],
            name: $data['name'],
            logoUrl: $data['logoUrl'],
            contracts: isset($data['contracts']) ? array_map(
                fn (array $contract) => CarrierContract::fromArray($contract),
                $data['contracts'],
            ) : null,
            hasMontonioContract: (bool) ($data['hasMontonioContract'] ?? false),
            supportedContractTypes: $data['supportedContractTypes'] ?? [],
        );
    }
}
