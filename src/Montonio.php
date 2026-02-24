<?php

declare(strict_types=1);

namespace Veltix\Montonio;

use Veltix\Montonio\Auth\JwtDecoder;
use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Http\HttpClient;
use Veltix\Montonio\Http\PaymentsHttpClient;
use Veltix\Montonio\Http\ShippingHttpClient;
use Veltix\Montonio\Payments\PaymentsClient;
use Veltix\Montonio\Shipping\ShippingClient;
use Veltix\Montonio\Webhook\WebhookVerifier;

final class Montonio
{
    private ?PaymentsClient $paymentsClient = null;

    private ?ShippingClient $shippingClient = null;

    private ?WebhookVerifier $webhookVerifier = null;

    private ?JwtFactory $jwtFactory = null;

    private ?JwtDecoder $jwtDecoder = null;

    private ?HttpClient $httpClient = null;

    public function __construct(
        private readonly Config $config,
    ) {}

    public function payments(): PaymentsClient
    {
        if ($this->paymentsClient === null) {
            $this->paymentsClient = new PaymentsClient(
                new PaymentsHttpClient(
                    $this->getHttpClient(),
                    $this->getJwtFactory(),
                    $this->config,
                ),
            );
        }

        return $this->paymentsClient;
    }

    public function shipping(): ShippingClient
    {
        if ($this->shippingClient === null) {
            $this->shippingClient = new ShippingClient(
                new ShippingHttpClient(
                    $this->getHttpClient(),
                    $this->getJwtFactory(),
                    $this->config,
                ),
            );
        }

        return $this->shippingClient;
    }

    public function webhooks(): WebhookVerifier
    {
        if ($this->webhookVerifier === null) {
            $this->webhookVerifier = new WebhookVerifier(
                $this->getJwtDecoder(),
            );
        }

        return $this->webhookVerifier;
    }

    private function getJwtFactory(): JwtFactory
    {
        if ($this->jwtFactory === null) {
            $this->jwtFactory = new JwtFactory($this->config);
        }

        return $this->jwtFactory;
    }

    private function getJwtDecoder(): JwtDecoder
    {
        if ($this->jwtDecoder === null) {
            $this->jwtDecoder = new JwtDecoder($this->config);
        }

        return $this->jwtDecoder;
    }

    private function getHttpClient(): HttpClient
    {
        if ($this->httpClient === null) {
            $this->httpClient = new HttpClient($this->config);
        }

        return $this->httpClient;
    }
}
