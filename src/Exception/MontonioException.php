<?php

declare(strict_types=1);

namespace Veltix\Montonio\Exception;

class MontonioException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        public readonly int $statusCode = 0,
        public readonly ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
