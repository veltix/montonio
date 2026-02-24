<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

use Veltix\Montonio\Payments\Enum\RefundStatus;
use Veltix\Montonio\Payments\Enum\RefundType;

final readonly class RefundResponse
{
    public function __construct(
        public string $uuid,
        public float $amount,
        public RefundStatus $status,
        public string $currency,
        public string $createdAt,
        public RefundType $type,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            amount: (float) $data['amount'],
            status: RefundStatus::from($data['status']),
            currency: $data['currency'],
            createdAt: $data['createdAt'],
            type: RefundType::from($data['type']),
        );
    }
}
