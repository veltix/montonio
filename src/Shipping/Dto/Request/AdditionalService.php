<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Dto\Request;

use Veltix\Montonio\Shipping\Enum\AdditionalServiceCode;

final readonly class AdditionalService
{
    public function __construct(
        public AdditionalServiceCode $code,
        public ?CodParams $params = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'code' => $this->code->value,
        ];

        if ($this->params !== null) {
            $data['params'] = $this->params->toArray();
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            code: AdditionalServiceCode::from($data['code']),
            params: isset($data['params']) ? CodParams::fromArray($data['params']) : null,
        );
    }
}
