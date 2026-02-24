<?php

declare(strict_types=1);

use Psr\Http\Client\ClientExceptionInterface;
use Veltix\Montonio\Exception\TransportException;
use Veltix\Montonio\Http\HttpClient;

use function Veltix\Montonio\Tests\jsonResponse;
use function Veltix\Montonio\Tests\mockPsrClient;
use function Veltix\Montonio\Tests\testConfig;

test('builds request with method, url, headers, and body', function () {
    $mock = mockPsrClient(jsonResponse(200, ['ok' => true]));
    $config = testConfig($mock->client);
    $client = new HttpClient($config);

    $response = $client->sendRequest(
        'POST',
        'https://example.com/api/test',
        ['Content-Type' => 'application/json', 'Authorization' => 'Bearer token'],
        '{"data":"value"}',
    );

    $request = $mock->lastRequest();

    expect($request->getMethod())->toBe('POST')
        ->and((string) $request->getUri())->toBe('https://example.com/api/test')
        ->and($request->getHeaderLine('content-type'))->toBe('application/json')
        ->and($request->getHeaderLine('authorization'))->toBe('Bearer token')
        ->and((string) $request->getBody())->toBe('{"data":"value"}');
});

test('does not attach body when null', function () {
    $mock = mockPsrClient(jsonResponse(200, []));
    $config = testConfig($mock->client);
    $client = new HttpClient($config);

    $client->sendRequest('GET', 'https://example.com/api');

    $request = $mock->lastRequest();
    expect((string) $request->getBody())->toBe('');
});

test('returns PSR response', function () {
    $expected = jsonResponse(200, ['status' => 'ok']);
    $mock = mockPsrClient($expected);
    $config = testConfig($mock->client);
    $client = new HttpClient($config);

    $response = $client->sendRequest('GET', 'https://example.com');

    expect($response)->toBe($expected)
        ->and($response->getStatusCode())->toBe(200);
});

test('wraps ClientExceptionInterface in TransportException', function () {
    $clientException = new class('Connection failed') extends \RuntimeException implements ClientExceptionInterface {};

    $psrClient = new class($clientException) implements \Psr\Http\Client\ClientInterface
    {
        public function __construct(private \Throwable $e) {}

        public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
        {
            throw $this->e;
        }
    };

    $config = testConfig($psrClient);
    $client = new HttpClient($config);

    try {
        $client->sendRequest('GET', 'https://example.com');
        test()->fail('Expected TransportException');
    } catch (TransportException $e) {
        expect($e->getMessage())->toBe('Connection failed')
            ->and($e->getPrevious())->toBe($clientException);
    }
});

test('adds multiple headers correctly', function () {
    $mock = mockPsrClient(jsonResponse(200, []));
    $config = testConfig($mock->client);
    $client = new HttpClient($config);

    $client->sendRequest('GET', 'https://example.com', [
        'Accept' => 'application/json',
        'X-Custom' => 'value',
    ]);

    $request = $mock->lastRequest();
    expect($request->getHeaderLine('accept'))->toBe('application/json')
        ->and($request->getHeaderLine('x-custom'))->toBe('value');
});

test('passes request through to PSR client', function () {
    $mock = mockPsrClient(jsonResponse(204, []));
    $config = testConfig($mock->client);
    $client = new HttpClient($config);

    $response = $client->sendRequest('DELETE', 'https://example.com/resource/123');

    expect($response->getStatusCode())->toBe(204)
        ->and($mock->lastRequest()->getMethod())->toBe('DELETE');
});
