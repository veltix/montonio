<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\CreatePaymentLinkRequest;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;

test('toArray includes required fields', function () {
    $request = new CreatePaymentLinkRequest(
        description: 'Test payment',
        currency: Currency::EUR,
        amount: 10.00,
        locale: Locale::EN,
        askAdditionalInfo: false,
        expiresAt: '2025-12-31T23:59:59Z',
    );

    $array = $request->toArray();
    expect($array['description'])->toBe('Test payment')
        ->and($array['currency'])->toBe('EUR')
        ->and($array['amount'])->toBe(10.00)
        ->and($array['locale'])->toBe('en')
        ->and($array['askAdditionalInfo'])->toBeFalse()
        ->and($array['expiresAt'])->toBe('2025-12-31T23:59:59Z')
        ->and($array)->not->toHaveKey('type')
        ->and($array)->not->toHaveKey('notificationUrl');
});

test('toArray includes optional fields when set', function () {
    $request = new CreatePaymentLinkRequest(
        description: 'Full test',
        currency: Currency::PLN,
        amount: 99.99,
        locale: Locale::PL,
        askAdditionalInfo: true,
        expiresAt: '2025-06-15T12:00:00Z',
        type: 'single',
        notificationUrl: 'https://example.com/notify',
        returnUrl: 'https://example.com/return',
        preferredProvider: 'MBANK',
        preferredCountry: 'PL',
        merchantReference: 'ref-001',
        paymentReference: 'pay-ref-001',
    );

    $array = $request->toArray();
    expect($array)->toHaveCount(13)
        ->and($array['type'])->toBe('single')
        ->and($array['notificationUrl'])->toBe('https://example.com/notify')
        ->and($array['merchantReference'])->toBe('ref-001');
});

test('toArray excludes null optional fields', function () {
    $request = new CreatePaymentLinkRequest(
        description: 'Test',
        currency: Currency::EUR,
        amount: 5.00,
        locale: Locale::ET,
        askAdditionalInfo: false,
        expiresAt: '2025-01-01T00:00:00Z',
        type: 'single',
    );

    $array = $request->toArray();
    expect($array)->toHaveKey('type')
        ->and($array)->not->toHaveKey('notificationUrl')
        ->and($array)->not->toHaveKey('returnUrl');
});

test('fromArray creates request with all fields', function () {
    $data = [
        'description' => 'From array',
        'currency' => 'EUR',
        'amount' => '50.00',
        'locale' => 'en',
        'askAdditionalInfo' => true,
        'expiresAt' => '2025-12-31T23:59:59Z',
        'type' => 'multi',
        'notificationUrl' => 'https://hook.example.com',
        'returnUrl' => 'https://return.example.com',
        'preferredProvider' => 'SEB',
        'preferredCountry' => 'EE',
        'merchantReference' => 'mref',
        'paymentReference' => 'pref',
    ];

    $request = CreatePaymentLinkRequest::fromArray($data);

    expect($request->description)->toBe('From array')
        ->and($request->currency)->toBe(Currency::EUR)
        ->and($request->amount)->toBe(50.00)
        ->and($request->locale)->toBe(Locale::EN)
        ->and($request->askAdditionalInfo)->toBeTrue()
        ->and($request->type)->toBe('multi')
        ->and($request->preferredProvider)->toBe('SEB');
});

test('roundtrip toArray/fromArray', function () {
    $original = new CreatePaymentLinkRequest(
        description: 'Roundtrip',
        currency: Currency::EUR,
        amount: 25.00,
        locale: Locale::LT,
        askAdditionalInfo: false,
        expiresAt: '2025-06-01T00:00:00Z',
        merchantReference: 'ref-rt',
    );

    $restored = CreatePaymentLinkRequest::fromArray($original->toArray());

    expect($restored->description)->toBe($original->description)
        ->and($restored->currency)->toBe($original->currency)
        ->and($restored->amount)->toBe($original->amount)
        ->and($restored->locale)->toBe($original->locale)
        ->and($restored->merchantReference)->toBe($original->merchantReference)
        ->and($restored->type)->toBeNull();
});
