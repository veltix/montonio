<?php

declare(strict_types=1);

use Veltix\Montonio\Montonio;
use Veltix\Montonio\Payments\PaymentsClient;
use Veltix\Montonio\Shipping\ShippingClient;
use Veltix\Montonio\Webhook\WebhookVerifier;

use function Veltix\Montonio\Tests\testConfig;

test('payments returns PaymentsClient', function () {
    $montonio = new Montonio(testConfig());

    expect($montonio->payments())->toBeInstanceOf(PaymentsClient::class);
});

test('shipping returns ShippingClient', function () {
    $montonio = new Montonio(testConfig());

    expect($montonio->shipping())->toBeInstanceOf(ShippingClient::class);
});

test('webhooks returns WebhookVerifier', function () {
    $montonio = new Montonio(testConfig());

    expect($montonio->webhooks())->toBeInstanceOf(WebhookVerifier::class);
});

test('payments returns same instance on subsequent calls', function () {
    $montonio = new Montonio(testConfig());

    expect($montonio->payments())->toBe($montonio->payments());
});

test('shipping returns same instance on subsequent calls', function () {
    $montonio = new Montonio(testConfig());

    expect($montonio->shipping())->toBe($montonio->shipping());
});

test('webhooks returns same instance on subsequent calls', function () {
    $montonio = new Montonio(testConfig());

    expect($montonio->webhooks())->toBe($montonio->webhooks());
});
