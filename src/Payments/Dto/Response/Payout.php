<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

final readonly class Payout
{
    public function __construct(
        public string $uuid,
        public string $storeUuid,
        public string $storeName,
        public string $storeLegalName,
        public string $iban,
        public string $accountName,
        public string $status,
        public string $settlementType,
        public string $paymentsAmount,
        public string $refundsAmount,
        public string $totalAmount,
        public string $currency,
        public ?string $expectedArrivalDate,
        public string $createdAt,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            storeUuid: $data['storeUuid'],
            storeName: $data['storeName'],
            storeLegalName: $data['storeLegalName'],
            iban: $data['iban'],
            accountName: $data['accountName'],
            status: $data['status'],
            settlementType: $data['settlementType'],
            paymentsAmount: (string) $data['paymentsAmount'],
            refundsAmount: (string) $data['refundsAmount'],
            totalAmount: (string) $data['totalAmount'],
            currency: $data['currency'],
            expectedArrivalDate: $data['expectedArrivalDate'] ?? null,
            createdAt: $data['createdAt'],
        );
    }
}
