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

final readonly class PaymentsHttpClient
{
    private string $baseUrl;

    public function __construct(
        private HttpClient $httpClient,
        private JwtFactory $jwtFactory,
        Config $config,
    ) {
        $this->baseUrl = $config->environment->paymentsBaseUrl();
    }

    /** @return array<string, mixed> */
    public function get(string $path): array
    {
        $response = $this->httpClient->sendRequest(
            'GET',
            $this->baseUrl.$path,
            [
                'Authorization' => 'Bearer '.$this->jwtFactory->createBearerToken(),
                'Accept' => 'application/json',
            ],
        );

        return $this->handleResponse($response);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload = []): array
    {
        $token = $this->jwtFactory->createDataToken($payload);

        $response = $this->httpClient->sendRequest(
            'POST',
            $this->baseUrl.$path,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            json_encode(['data' => $token], JSON_THROW_ON_ERROR),
        );

        return $this->handleResponse($response);
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
