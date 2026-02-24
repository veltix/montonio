<?php

declare(strict_types=1);

namespace Veltix\Montonio\Http;

use Psr\Http\Message\ResponseInterface;
use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Config;
use Veltix\Montonio\Exception\ApiException;
use Veltix\Montonio\Exception\AuthenticationException;
use Veltix\Montonio\Exception\ConflictException;
use Veltix\Montonio\Exception\NotFoundException;
use Veltix\Montonio\Exception\ValidationException;

final readonly class ShippingHttpClient
{
    private string $baseUrl;

    public function __construct(
        private HttpClient $httpClient,
        private JwtFactory $jwtFactory,
        Config $config,
    ) {
        $this->baseUrl = $config->environment->shippingBaseUrl();
    }

    /**
     * @param  array<string, string>  $queryParams
     * @return array<string, mixed>
     */
    public function get(string $path, array $queryParams = []): array
    {
        $url = $this->baseUrl.$path;

        if ($queryParams !== []) {
            $url .= '?'.http_build_query($queryParams);
        }

        $response = $this->httpClient->sendRequest(
            'GET',
            $url,
            [
                'Authorization' => 'Bearer '.$this->jwtFactory->createBearerToken(),
                'Accept' => 'application/json',
            ],
        );

        return $this->handleResponse($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $queryParams
     * @return array<string, mixed>
     */
    public function post(string $path, array $body = [], array $queryParams = []): array
    {
        $url = $this->baseUrl.$path;

        if ($queryParams !== []) {
            $url .= '?'.http_build_query($queryParams);
        }

        $response = $this->httpClient->sendRequest(
            'POST',
            $url,
            [
                'Authorization' => 'Bearer '.$this->jwtFactory->createBearerToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            json_encode($body, JSON_THROW_ON_ERROR),
        );

        return $this->handleResponse($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function patch(string $path, array $body = []): array
    {
        $response = $this->httpClient->sendRequest(
            'PATCH',
            $this->baseUrl.$path,
            [
                'Authorization' => 'Bearer '.$this->jwtFactory->createBearerToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            json_encode($body, JSON_THROW_ON_ERROR),
        );

        return $this->handleResponse($response);
    }

    public function delete(string $path): void
    {
        $response = $this->httpClient->sendRequest(
            'DELETE',
            $this->baseUrl.$path,
            [
                'Authorization' => 'Bearer '.$this->jwtFactory->createBearerToken(),
                'Accept' => 'application/json',
            ],
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        $body = (string) $response->getBody();

        match (true) {
            $statusCode === 401, $statusCode === 403 => throw new AuthenticationException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
            $statusCode === 404 => throw new NotFoundException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
            default => throw new ApiException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
        };
    }

    /** @return array<string, mixed> */
    private function handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($statusCode >= 200 && $statusCode < 300) {
            /** @var array<string, mixed> $decoded */
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            return $decoded;
        }

        match (true) {
            $statusCode === 401, $statusCode === 403 => throw new AuthenticationException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
            $statusCode === 400, $statusCode === 422 => throw new ValidationException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
            $statusCode === 404 => throw new NotFoundException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
            $statusCode === 409 => throw new ConflictException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
            default => throw new ApiException(
                message: $body,
                statusCode: $statusCode,
                responseBody: $body,
            ),
        };
    }
}
