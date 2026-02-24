<?php

declare(strict_types=1);

namespace Veltix\Montonio\Auth;

use Firebase\JWT\JWT;
use Veltix\Montonio\Config;

final readonly class JwtFactory
{
    public function __construct(
        private Config $config,
    ) {}

    public function createBearerToken(): string
    {
        $payload = [
            'accessKey' => $this->config->accessKey,
            'iat' => time(),
            'exp' => time() + $this->config->jwtTtl,
        ];

        return JWT::encode($payload, $this->config->secretKey, 'HS256');
    }

    /** @param array<string, mixed> $payload */
    public function createDataToken(array $payload): string
    {
        $payload = array_merge($payload, [
            'accessKey' => $this->config->accessKey,
            'iat' => time(),
            'exp' => time() + $this->config->jwtTtl,
        ]);

        return JWT::encode($payload, $this->config->secretKey, 'HS256');
    }
}
