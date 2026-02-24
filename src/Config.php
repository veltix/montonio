<?php

declare(strict_types=1);

namespace Veltix\Montonio;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class Config
{
    public function __construct(
        public string $accessKey,
        public string $secretKey,
        public Environment $environment,
        public ClientInterface $httpClient,
        public RequestFactoryInterface $requestFactory,
        public StreamFactoryInterface $streamFactory,
        public int $jwtTtl = 3600,
        public int $jwtLeeway = 300,
    ) {}
}
