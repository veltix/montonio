<?php

declare(strict_types=1);

use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Exception\ApiException;
use Veltix\Montonio\Exception\AuthenticationException;
use Veltix\Montonio\Exception\ConflictException;
use Veltix\Montonio\Exception\NotFoundException;
use Veltix\Montonio\Exception\ValidationException;
use Veltix\Montonio\Http\HttpClient;
use Veltix\Montonio\Http\ShippingHttpClient;

use function Veltix\Montonio\Tests\jsonResponse;
use function Veltix\Montonio\Tests\mockPsrClient;
use function Veltix\Montonio\Tests\rawResponse;
use function Veltix\Montonio\Tests\testConfig;

function shippingHttpClient(array $responses): array
{
    $mock = mockPsrClient(...$responses);
    $config = testConfig($mock->client);
    $httpClient = new HttpClient($config);
    $jwtFactory = new JwtFactory($config);
    $client = new ShippingHttpClient($httpClient, $jwtFactory, $config);

    return [$client, $mock];
}

test('GET adds Bearer authorization header', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, ['data' => 'test'])]);

    $client->get('/carriers');

    $request = $mock->lastRequest();
    expect($request->getHeaderLine('authorization'))->toStartWith('Bearer ')
        ->and($request->getHeaderLine('accept'))->toBe('application/json')
        ->and($request->getMethod())->toBe('GET');
});

test('GET builds correct sandbox URL', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, [])]);

    $client->get('/shipments/123');

    expect((string) $mock->lastRequest()->getUri())
        ->toBe('https://sandbox-shipping.montonio.com/api/v2/shipments/123');
});

test('GET appends query params', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, [])]);

    $client->get('/shipping-methods/pickup-points', [
        'carrierCode' => 'omniva',
        'countryCode' => 'EE',
    ]);

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('carrierCode=omniva')
        ->and($url)->toContain('countryCode=EE');
});

test('GET without query params has no question mark', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, [])]);

    $client->get('/carriers');

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->not->toContain('?');
});

test('GET returns decoded JSON', function () {
    [$client] = shippingHttpClient([jsonResponse(200, ['carriers' => []])]);

    $result = $client->get('/carriers');

    expect($result)->toBe(['carriers' => []]);
});

test('POST sends Bearer and JSON body', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, ['id' => 'ship-1'])]);

    $client->post('/shipments', ['receiver' => ['name' => 'John']]);

    $request = $mock->lastRequest();
    expect($request->getMethod())->toBe('POST')
        ->and($request->getHeaderLine('authorization'))->toStartWith('Bearer ')
        ->and($request->getHeaderLine('content-type'))->toBe('application/json')
        ->and(json_decode((string) $request->getBody(), true))->toBe(['receiver' => ['name' => 'John']]);
});

test('POST with query params', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, [])]);

    $client->post('/shipping-methods/filter-by-parcels', ['parcels' => []], ['destination' => 'EE']);

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('destination=EE');
});

test('PATCH sends Bearer and JSON body', function () {
    [$client, $mock] = shippingHttpClient([jsonResponse(200, ['id' => 'ship-1'])]);

    $client->patch('/shipments/ship-1', ['receiver' => ['name' => 'Jane']]);

    $request = $mock->lastRequest();
    expect($request->getMethod())->toBe('PATCH')
        ->and($request->getHeaderLine('authorization'))->toStartWith('Bearer ')
        ->and($request->getHeaderLine('content-type'))->toBe('application/json');
});

test('DELETE sends Bearer header and returns void', function () {
    [$client, $mock] = shippingHttpClient([rawResponse(204, '')]);

    $result = $client->delete('/webhooks/wh-1');

    expect($result)->toBeNull()
        ->and($mock->lastRequest()->getMethod())->toBe('DELETE')
        ->and($mock->lastRequest()->getHeaderLine('authorization'))->toStartWith('Bearer ');
});

test('throws AuthenticationException on 401', function () {
    [$client] = shippingHttpClient([rawResponse(401, 'Unauthorized')]);
    $client->get('/test');
})->throws(AuthenticationException::class);

test('throws AuthenticationException on 403', function () {
    [$client] = shippingHttpClient([rawResponse(403, 'Forbidden')]);
    $client->get('/test');
})->throws(AuthenticationException::class);

test('throws ValidationException on 400', function () {
    [$client] = shippingHttpClient([rawResponse(400, 'Bad Request')]);
    $client->get('/test');
})->throws(ValidationException::class);

test('throws ValidationException on 422', function () {
    [$client] = shippingHttpClient([rawResponse(422, 'Unprocessable')]);
    $client->get('/test');
})->throws(ValidationException::class);

test('throws NotFoundException on 404', function () {
    [$client] = shippingHttpClient([rawResponse(404, 'Not Found')]);
    $client->get('/test');
})->throws(NotFoundException::class);

test('throws ConflictException on 409', function () {
    [$client] = shippingHttpClient([rawResponse(409, 'Conflict')]);
    $client->get('/test');
})->throws(ConflictException::class);

test('throws ApiException on 500', function () {
    [$client] = shippingHttpClient([rawResponse(500, 'Server Error')]);
    try {
        $client->get('/test');
        test()->fail('Expected ApiException');
    } catch (ApiException $e) {
        expect($e->statusCode)->toBe(500)
            ->and($e->responseBody)->toBe('Server Error');
    }
});

test('DELETE throws AuthenticationException on 401', function () {
    [$client] = shippingHttpClient([rawResponse(401, 'Unauthorized')]);
    $client->delete('/webhooks/1');
})->throws(AuthenticationException::class);

test('DELETE throws NotFoundException on 404', function () {
    [$client] = shippingHttpClient([rawResponse(404, 'Not Found')]);
    $client->delete('/webhooks/1');
})->throws(NotFoundException::class);

test('DELETE throws ApiException on 500', function () {
    [$client] = shippingHttpClient([rawResponse(500, 'Error')]);
    $client->delete('/webhooks/1');
})->throws(ApiException::class);
