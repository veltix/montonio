<?php

declare(strict_types=1);

namespace Veltix\Montonio\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Veltix\Montonio\Config;

final readonly class JwtDecoder
{
    public function __construct(
        private Config $config,
    ) {}

    public function decode(string $token): object
    {
        JWT::$leeway = $this->config->jwtLeeway;

        return JWT::decode(
            $token,
            new Key($this->config->secretKey, 'HS256'),
        );
    }
}
