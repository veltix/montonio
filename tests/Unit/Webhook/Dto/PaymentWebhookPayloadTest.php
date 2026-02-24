<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Enum\PaymentStatus;
use Veltix\Montonio\Webhook\Dto\PaymentWebhookPayload;

test('fromObject maps all fields', function () {
    $data = (object) [
        'uuid' => 'uuid-1',
        'accessKey' => 'key-1',
        'merchantReference' => 'ref-1',
        'merchantReferenceDisplay' => 'REF-1',
        'paymentStatus' => 'PAID',
        'paymentMethod' => 'paymentInitiation',
        'grandTotal' => 99.99,
        'currency' => 'EUR',
        'senderIban' => 'EE12345',
        'senderName' => 'John Doe',
        'paymentProviderName' => 'Swedbank',
        'paymentLinkUuid' => 'pl-uuid-1',
        'iat' => 1700000000,
        'exp' => 1700003600,
    ];

    $payload = PaymentWebhookPayload::fromObject($data);

    expect($payload->uuid)->toBe('uuid-1')
        ->and($payload->accessKey)->toBe('key-1')
        ->and($payload->merchantReference)->toBe('ref-1')
        ->and($payload->merchantReferenceDisplay)->toBe('REF-1')
        ->and($payload->paymentMethod)->toBe('paymentInitiation')
        ->and($payload->grandTotal)->toBe(99.99)
        ->and($payload->currency)->toBe('EUR')
        ->and($payload->senderIban)->toBe('EE12345')
        ->and($payload->senderName)->toBe('John Doe')
        ->and($payload->paymentProviderName)->toBe('Swedbank')
        ->and($payload->paymentLinkUuid)->toBe('pl-uuid-1')
        ->and($payload->iat)->toBe(1700000000)
        ->and($payload->exp)->toBe(1700003600);
});

test('maps PaymentStatus enum', function () {
    $data = (object) [
        'uuid' => 'u',
        'accessKey' => 'k',
        'merchantReference' => 'r',
        'paymentStatus' => 'PARTIALLY_REFUNDED',
        'grandTotal' => 50,
        'currency' => 'EUR',
        'iat' => 1,
        'exp' => 2,
    ];

    $payload = PaymentWebhookPayload::fromObject($data);

    expect($payload->paymentStatus)->toBe(PaymentStatus::PARTIALLY_REFUNDED);
});

test('handles nullable fields', function () {
    $data = (object) [
        'uuid' => 'u',
        'accessKey' => 'k',
        'merchantReference' => 'r',
        'paymentStatus' => 'PENDING',
        'grandTotal' => 10,
        'currency' => 'EUR',
        'iat' => 1,
        'exp' => 2,
    ];

    $payload = PaymentWebhookPayload::fromObject($data);

    expect($payload->merchantReferenceDisplay)->toBeNull()
        ->and($payload->paymentMethod)->toBeNull()
        ->and($payload->senderIban)->toBeNull()
        ->and($payload->senderName)->toBeNull()
        ->and($payload->paymentProviderName)->toBeNull()
        ->and($payload->paymentLinkUuid)->toBeNull();
});

test('casts grandTotal to float and iat/exp to int', function () {
    $data = (object) [
        'uuid' => 'u',
        'accessKey' => 'k',
        'merchantReference' => 'r',
        'paymentStatus' => 'PAID',
        'grandTotal' => '123.45',
        'currency' => 'EUR',
        'iat' => '1700000000',
        'exp' => '1700003600',
    ];

    $payload = PaymentWebhookPayload::fromObject($data);

    expect($payload->grandTotal)->toBe(123.45)
        ->and($payload->grandTotal)->toBeFloat()
        ->and($payload->iat)->toBe(1700000000)
        ->and($payload->iat)->toBeInt()
        ->and($payload->exp)->toBe(1700003600)
        ->and($payload->exp)->toBeInt();
});

test('fromObject with docs fixture', function () {
    $fixture = \Veltix\Montonio\Tests\fixture('Payments/webhook-payload.json');
    $data = json_decode(json_encode($fixture));

    $payload = PaymentWebhookPayload::fromObject($data);

    expect($payload->uuid)->toBe('the-montonio-order-uuid')
        ->and($payload->accessKey)->toBe('MY_ACCESS_KEY')
        ->and($payload->merchantReference)->toBe('MY-ORDER-ID-123')
        ->and($payload->paymentStatus)->toBe(PaymentStatus::PAID)
        ->and($payload->paymentMethod)->toBe('paymentInitiation')
        ->and($payload->grandTotal)->toBe(99.99)
        ->and($payload->currency)->toBe('EUR')
        ->and($payload->senderIban)->toBe('EE471000001020145685')
        ->and($payload->senderName)->toBe('John Doe')
        ->and($payload->paymentProviderName)->toBe('New Wave Bank Group')
        ->and($payload->iat)->toBe(1632967333)
        ->and($payload->exp)->toBe(1632967333);
});
