<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Veltix\Montonio\Auth\JwtDecoder;
use Veltix\Montonio\Payments\Enum\PaymentStatus;
use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;
use Veltix\Montonio\Webhook\Dto\PaymentWebhookPayload;
use Veltix\Montonio\Webhook\Dto\ShippingWebhookPayload;
use Veltix\Montonio\Webhook\WebhookVerifier;

use function Veltix\Montonio\Tests\testConfig;

test('verifyPaymentWebhook decodes and returns typed DTO', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);
    $verifier = new WebhookVerifier($decoder);

    $payload = [
        'uuid' => 'order-uuid-123',
        'accessKey' => 'test_access_key',
        'merchantReference' => 'ref-001',
        'merchantReferenceDisplay' => 'REF-001',
        'paymentStatus' => 'PAID',
        'paymentMethod' => 'paymentInitiation',
        'grandTotal' => 100.50,
        'currency' => 'EUR',
        'senderIban' => 'EE123456',
        'senderName' => 'John Doe',
        'paymentProviderName' => 'Swedbank',
        'paymentLinkUuid' => null,
        'iat' => time(),
        'exp' => time() + 3600,
    ];

    $token = JWT::encode($payload, $config->secretKey, 'HS256');
    $result = $verifier->verifyPaymentWebhook($token);

    expect($result)->toBeInstanceOf(PaymentWebhookPayload::class)
        ->and($result->uuid)->toBe('order-uuid-123')
        ->and($result->accessKey)->toBe('test_access_key')
        ->and($result->merchantReference)->toBe('ref-001')
        ->and($result->paymentStatus)->toBe(PaymentStatus::PAID)
        ->and($result->grandTotal)->toBe(100.50)
        ->and($result->currency)->toBe('EUR')
        ->and($result->senderIban)->toBe('EE123456')
        ->and($result->senderName)->toBe('John Doe')
        ->and($result->paymentProviderName)->toBe('Swedbank');
});

test('verifyShippingWebhook decodes and returns typed DTO', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);
    $verifier = new WebhookVerifier($decoder);

    $payload = [
        'eventId' => 'evt-123',
        'shipmentId' => 'ship-456',
        'created' => '2025-01-15T10:00:00Z',
        'data' => ['status' => 'registered'],
        'eventType' => 'shipment.registered',
        'iat' => time(),
        'exp' => time() + 3600,
    ];

    $token = JWT::encode($payload, $config->secretKey, 'HS256');
    $result = $verifier->verifyShippingWebhook($token);

    expect($result)->toBeInstanceOf(ShippingWebhookPayload::class)
        ->and($result->eventId)->toBe('evt-123')
        ->and($result->shipmentId)->toBe('ship-456')
        ->and($result->created)->toBe('2025-01-15T10:00:00Z')
        ->and($result->data)->toBe(['status' => 'registered'])
        ->and($result->eventType)->toBe(ShippingWebhookEvent::ShipmentRegistered);
});

test('verifyPaymentWebhook throws on invalid token', function () {
    $config = testConfig();
    $decoder = new JwtDecoder($config);
    $verifier = new WebhookVerifier($decoder);

    $verifier->verifyPaymentWebhook('invalid.token.here');
})->throws(\Exception::class);
