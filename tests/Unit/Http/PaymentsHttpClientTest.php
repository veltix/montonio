<?php

declare(strict_types=1);

use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Exception\ApiException;
use Veltix\Montonio\Exception\AuthenticationException;
use Veltix\Montonio\Exception\ConflictException;
use Veltix\Montonio\Exception\NotFoundException;
use Veltix\Montonio\Exception\ValidationException;
use Veltix\Montonio\Http\HttpClient;
use Veltix\Montonio\Http\PaymentsHttpClient;

use function Veltix\Montonio\Tests\jsonResponse;
use function Veltix\Montonio\Tests\mockPsrClient;
use function Veltix\Montonio\Tests\rawResponse;
use function Veltix\Montonio\Tests\testConfig;

function paymentsClient(array $responses): array
{
    $mock = mockPsrClient(...$responses);
    $config = testConfig($mock->client);
    $httpClient = new HttpClient($config);
    $jwtFactory = new JwtFactory($config);
    $client = new PaymentsHttpClient($httpClient, $jwtFactory, $config);

    return [$client, $mock];
}

test('GET adds Bearer authorization header', function () {
    [$client, $mock] = paymentsClient([jsonResponse(200, ['data' => 'test'])]);

    $client->get('/stores/payment-methods');

    $request = $mock->lastRequest();
    expect($request->getHeaderLine('authorization'))->toStartWith('Bearer ')
        ->and($request->getHeaderLine('accept'))->toBe('application/json')
        ->and($request->getMethod())->toBe('GET');
});

test('GET returns decoded JSON', function () {
    [$client] = paymentsClient([jsonResponse(200, ['foo' => 'bar'])]);

    $result = $client->get('/test');

    expect($result)->toBe(['foo' => 'bar']);
});

test('GET builds correct URL with sandbox base', function () {
    [$client, $mock] = paymentsClient([jsonResponse(200, [])]);

    $client->get('/orders/123');

    expect((string) $mock->lastRequest()->getUri())
        ->toBe('https://sandbox-stargate.montonio.com/api/orders/123');
});

test('POST wraps payload in JWT data token', function () {
    [$client, $mock] = paymentsClient([jsonResponse(200, ['created' => true])]);

    $client->post('/orders', ['merchantReference' => 'ref-123']);

    $request = $mock->lastRequest();
    $body = json_decode((string) $request->getBody(), true);

    expect($body)->toHaveKey('data')
        ->and($request->getHeaderLine('content-type'))->toBe('application/json')
        ->and($request->getMethod())->toBe('POST');
});

test('POST returns decoded JSON response', function () {
    [$client] = paymentsClient([jsonResponse(201, ['uuid' => 'abc-123'])]);

    $result = $client->post('/orders', []);

    expect($result)->toBe(['uuid' => 'abc-123']);
});

test('POST with empty payload works', function () {
    [$client, $mock] = paymentsClient([jsonResponse(200, ['uuid' => 'sess-1'])]);

    $result = $client->post('/sessions');

    expect($result)->toBe(['uuid' => 'sess-1']);
    $body = json_decode((string) $mock->lastRequest()->getBody(), true);
    expect($body)->toHaveKey('data');
});

test('throws AuthenticationException on 401', function () {
    [$client] = paymentsClient([rawResponse(401, 'Unauthorized')]);

    $client->get('/test');
})->throws(AuthenticationException::class);

test('throws AuthenticationException on 403', function () {
    [$client] = paymentsClient([rawResponse(403, 'Forbidden')]);

    $client->get('/test');
})->throws(AuthenticationException::class);

test('throws ValidationException on 400', function () {
    [$client] = paymentsClient([rawResponse(400, 'Bad Request')]);

    $client->get('/test');
})->throws(ValidationException::class);

test('throws ValidationException on 422', function () {
    [$client] = paymentsClient([rawResponse(422, 'Unprocessable')]);

    $client->get('/test');
})->throws(ValidationException::class);

test('throws NotFoundException on 404', function () {
    [$client] = paymentsClient([rawResponse(404, 'Not Found')]);

    $client->get('/test');
})->throws(NotFoundException::class);

test('throws ConflictException on 409', function () {
    [$client] = paymentsClient([rawResponse(409, 'Conflict')]);

    $client->get('/test');
})->throws(ConflictException::class);

test('throws ApiException on 500', function () {
    [$client] = paymentsClient([rawResponse(500, 'Internal Server Error')]);

    try {
        $client->get('/test');
        test()->fail('Expected ApiException');
    } catch (ApiException $e) {
        expect($e->statusCode)->toBe(500)
            ->and($e->responseBody)->toBe('Internal Server Error');
    }
});
