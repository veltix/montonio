<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Response;

use Veltix\Montonio\Shipping\Enum\LabelFileStatus;

final readonly class LabelFileResponse
{
    public function __construct(
        public string $id,
        public LabelFileStatus $status,
        public ?string $pageSize = null,
        public ?int $labelsPerPage = null,
        public ?string $orderLabelsBy = null,
        public ?string $labelFileUrl = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: LabelFileStatus::from($data['status']),
            pageSize: $data['pageSize'] ?? null,
            labelsPerPage: $data['labelsPerPage'] ?? null,
            orderLabelsBy: $data['orderLabelsBy'] ?? null,
            labelFileUrl: $data['labelFileUrl'] ?? null,
        );
    }
}
