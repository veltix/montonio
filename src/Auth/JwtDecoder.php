<?php

declare(strict_types=1);

namespace Veltix\Montonio\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Veltix\Montonio\Config;
use Veltix\Montonio\Support\Json;

final readonly class JwtDecoder
{
    public function __construct(
        private Config $config,
    ) {}

    /**
     * Verify $token (HS256 with the secret key, its expiry within the
     * leeway) and return its claims. A number with a fraction is a decimal
     * string ("99.99"), never a float.
     */
    public function decode(string $token): object
    {
        JWT::$leeway = $this->config->jwtLeeway;

        JWT::decode(
            $token,
            new Key($this->config->secretKey, 'HS256'),
        );

        $claims = Json::decode(JWT::urlsafeB64Decode(explode('.', $token)[1]));

        $decoded = json_decode(json_encode($claims, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);

        return is_object($decoded) ? $decoded : (object) [];
    }
}
