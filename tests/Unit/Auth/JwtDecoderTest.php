<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Veltix\Montonio\Auth\JwtDecoder;

use function Veltix\Montonio\Tests\testConfig;

test('decodes a valid JWT', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);

    $payload = [
        'accessKey' => 'test_access_key',
        'iat' => time(),
        'exp' => time() + 3600,
        'data' => 'hello',
    ];

    $token = JWT::encode($payload, $config->secretKey, 'HS256');
    $decoded = $decoder->decode($token);

    expect($decoded->accessKey)->toBe('test_access_key')
        ->and($decoded->data)->toBe('hello');
});

test('returns an object', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);

    $token = JWT::encode(['iat' => time(), 'exp' => time() + 3600], $config->secretKey, 'HS256');
    $decoded = $decoder->decode($token);

    expect($decoded)->toBeObject();
});

test('leeway allows near-expired tokens', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);

    $token = JWT::encode([
        'iat' => time() - 7200,
        'exp' => time() - 100,
    ], $config->secretKey, 'HS256');

    $decoded = $decoder->decode($token);

    expect($decoded)->toBeObject();
});

test('throws on invalid token', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);

    $decoder->decode('invalid.token.here');
})->throws(\Exception::class);
