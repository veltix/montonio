<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

use Veltix\Montonio\Support\Decimal;

final readonly class CreateRefundRequest
{
    public function __construct(
        public string $orderUuid,
        public string $amount,
        public string $idempotencyKey,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'orderUuid' => $this->orderUuid,
            'amount' => Decimal::toNumber($this->amount),
            'idempotencyKey' => $this->idempotencyKey,
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            orderUuid: $data['orderUuid'],
            amount: Decimal::fromValue($data['amount']),
            idempotencyKey: $data['idempotencyKey'],
        );
    }
}
