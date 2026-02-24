<?php

declare(strict_types=1);

namespace Veltix\Montonio\Http;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Veltix\Montonio\Config;
use Veltix\Montonio\Exception\TransportException;

final readonly class HttpClient
{
    public function __construct(
        private Config $config,
    ) {}

    /** @param array<string, string> $headers */
    public function sendRequest(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
    ): ResponseInterface {
        try {
            $request = $this->config->requestFactory->createRequest($method, $url);

            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }

            if ($body !== null) {
                $stream = $this->config->streamFactory->createStream($body);
                $request = $request->withBody($stream);
            }

            return $this->config->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException(
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }
}
