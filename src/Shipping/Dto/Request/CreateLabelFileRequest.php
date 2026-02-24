<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Shipping\Enum\OrderLabelsBy;
use Veltix\Montonio\Shipping\Enum\PageSize;

final readonly class CreateLabelFileRequest
{
    /**
     * @param  string[]  $shipmentIds
     */
    public function __construct(
        public array $shipmentIds,
        public ?PageSize $pageSize = null,
        public ?int $labelsPerPage = null,
        public ?OrderLabelsBy $orderLabelsBy = null,
        public ?bool $synchronous = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'shipmentIds' => $this->shipmentIds,
        ];

        if ($this->pageSize !== null) {
            $data['pageSize'] = $this->pageSize->value;
        }

        if ($this->labelsPerPage !== null) {
            $data['labelsPerPage'] = $this->labelsPerPage;
        }

        if ($this->orderLabelsBy !== null) {
            $data['orderLabelsBy'] = $this->orderLabelsBy->value;
        }

        if ($this->synchronous !== null) {
            $data['synchronous'] = $this->synchronous;
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            shipmentIds: $data['shipmentIds'],
            pageSize: isset($data['pageSize']) ? PageSize::from($data['pageSize']) : null,
            labelsPerPage: $data['labelsPerPage'] ?? null,
            orderLabelsBy: isset($data['orderLabelsBy']) ? OrderLabelsBy::from($data['orderLabelsBy']) : null,
            synchronous: $data['synchronous'] ?? null,
        );
    }
}
