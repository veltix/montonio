<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Config;
use Veltix\Montonio\Environment;

use function Veltix\Montonio\Tests\testConfig;
use function Veltix\Montonio\Tests\testJwtFactory;

test('bearer token contains accessKey, iat, exp', function () {
    $config = testConfig();
    $factory = new JwtFactory($config);

    $token = $factory->createBearerToken();
    $decoded = JWT::decode($token, new Key($config->secretKey, 'HS256'));

    expect($decoded->accessKey)->toBe('test_access_key')
        ->and($decoded->iat)->toBeInt()
        ->and($decoded->exp)->toBeInt()
        ->and($decoded->exp - $decoded->iat)->toBe(3600);
});

test('data token merges payload with standard claims', function () {
    $config = testConfig();
    $factory = new JwtFactory($config);

    $token = $factory->createDataToken(['foo' => 'bar', 'amount' => 12.50]);
    $decoded = JWT::decode($token, new Key($config->secretKey, 'HS256'));

    expect($decoded->foo)->toBe('bar')
        ->and($decoded->amount)->toBe(12.50)
        ->and($decoded->accessKey)->toBe('test_access_key')
        ->and($decoded->iat)->toBeInt()
        ->and($decoded->exp)->toBeInt();
});

test('custom jwtTtl is respected', function () {
    $config = new Config(
        accessKey: 'k',
        secretKey: 'test_secret_key_long_enough_for_hmac256',
        environment: Environment::Sandbox,
        httpClient: \Veltix\Montonio\Tests\mockPsrClient(\Veltix\Montonio\Tests\jsonResponse(200, []))->client,
        requestFactory: \Veltix\Montonio\Tests\mockRequestFactory(),
        streamFactory: \Veltix\Montonio\Tests\mockStreamFactory(),
        jwtTtl: 900,
    );

    $factory = new JwtFactory($config);
    $token = $factory->createBearerToken();
    $decoded = JWT::decode($token, new Key($config->secretKey, 'HS256'));

    expect($decoded->exp - $decoded->iat)->toBe(900);
});

test('tokens are signed with HS256', function () {
    $factory = testJwtFactory();

    $token = $factory->createBearerToken();
    $parts = explode('.', $token);

    expect($parts)->toHaveCount(3);

    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);

    expect($header['alg'])->toBe('HS256')
        ->and($header['typ'])->toBe('JWT');
});

test('data token payload keys override is correct order', function () {
    $config = testConfig();
    $factory = new JwtFactory($config);

    $token = $factory->createDataToken(['accessKey' => 'should_be_overridden']);
    $decoded = JWT::decode($token, new Key($config->secretKey, 'HS256'));

    expect($decoded->accessKey)->toBe('test_access_key');
});
