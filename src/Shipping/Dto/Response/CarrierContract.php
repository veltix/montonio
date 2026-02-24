<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

final readonly class CarrierContract
{
    public function __construct(
        public string $id,
        public string $carrierId,
        public string $country,
        public ?string $lastUsedParcelNumber,
        public ?int $daysAllowedForReturns,
        public bool $isDirectContract,
        public string $createdAt,
        public bool $returnsAllowed,
        public ?string $parcelHandoverMethod,
        public ?string $defaultLockerSize,
        public ?string $logisticsContractNumber,
        public ContractCredentials $credentials,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            carrierId: $data['carrierId'],
            country: $data['country'],
            lastUsedParcelNumber: isset($data['lastUsedParcelNumber']) ? (string) $data['lastUsedParcelNumber'] : null,
            daysAllowedForReturns: $data['daysAllowedForReturns'] ?? null,
            isDirectContract: (bool) $data['isDirectContract'],
            createdAt: $data['createdAt'],
            returnsAllowed: (bool) $data['returnsAllowed'],
            parcelHandoverMethod: $data['parcelHandoverMethod'] ?? null,
            defaultLockerSize: $data['defaultLockerSize'] ?? null,
            logisticsContractNumber: $data['logisticsContractNumber'] ?? null,
            credentials: ContractCredentials::fromArray($data['credentials'] ?? []),
        );
    }
}
