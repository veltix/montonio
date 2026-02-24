<?php

declare(strict_types=1);

use Veltix\Montonio\Config;
use Veltix\Montonio\Environment;

use function Veltix\Montonio\Tests\jsonResponse;
use function Veltix\Montonio\Tests\mockPsrClient;
use function Veltix\Montonio\Tests\mockRequestFactory;
use function Veltix\Montonio\Tests\mockStreamFactory;

test('stores all properties', function () {
    $mock = mockPsrClient(jsonResponse(200, []));
    $requestFactory = mockRequestFactory();
    $streamFactory = mockStreamFactory();

    $config = new Config(
        accessKey: 'my_key',
        secretKey: 'my_secret',
        environment: Environment::Production,
        httpClient: $mock->client,
        requestFactory: $requestFactory,
        streamFactory: $streamFactory,
    );

    expect($config->accessKey)->toBe('my_key')
        ->and($config->secretKey)->toBe('my_secret')
        ->and($config->environment)->toBe(Environment::Production)
        ->and($config->httpClient)->toBe($mock->client)
        ->and($config->requestFactory)->toBe($requestFactory)
        ->and($config->streamFactory)->toBe($streamFactory);
});

test('defaults jwtTtl to 3600', function () {
    $config = new Config(
        accessKey: 'k',
        secretKey: 's',
        environment: Environment::Sandbox,
        httpClient: mockPsrClient(jsonResponse(200, []))->client,
        requestFactory: mockRequestFactory(),
        streamFactory: mockStreamFactory(),
    );

    expect($config->jwtTtl)->toBe(3600);
});

test('defaults jwtLeeway to 300', function () {
    $config = new Config(
        accessKey: 'k',
        secretKey: 's',
        environment: Environment::Sandbox,
        httpClient: mockPsrClient(jsonResponse(200, []))->client,
        requestFactory: mockRequestFactory(),
        streamFactory: mockStreamFactory(),
    );

    expect($config->jwtLeeway)->toBe(300);
});

test('accepts custom jwtTtl and jwtLeeway', function () {
    $config = new Config(
        accessKey: 'k',
        secretKey: 's',
        environment: Environment::Sandbox,
        httpClient: mockPsrClient(jsonResponse(200, []))->client,
        requestFactory: mockRequestFactory(),
        streamFactory: mockStreamFactory(),
        jwtTtl: 7200,
        jwtLeeway: 60,
    );

    expect($config->jwtTtl)->toBe(7200)
        ->and($config->jwtLeeway)->toBe(60);
});
