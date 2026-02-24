<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\Address;
use Veltix\Montonio\Payments\Dto\Request\CreateOrderRequest;
use Veltix\Montonio\Payments\Dto\Request\LineItem;
use Veltix\Montonio\Payments\Dto\Request\Payment;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;
use Veltix\Montonio\Payments\Enum\PaymentMethodCode;

test('toArray includes required fields', function () {
    $request = new CreateOrderRequest(
        merchantReference: 'order-123',
        returnUrl: 'https://example.com/return',
        notificationUrl: 'https://example.com/notify',
        grandTotal: 100.00,
        currency: Currency::EUR,
        locale: Locale::EN,
        payment: new Payment(amount: 100.00, currency: Currency::EUR, method: PaymentMethodCode::PaymentInitiation),
    );

    $array = $request->toArray();
    expect($array['merchantReference'])->toBe('order-123')
        ->and($array['returnUrl'])->toBe('https://example.com/return')
        ->and($array['notificationUrl'])->toBe('https://example.com/notify')
        ->and($array['grandTotal'])->toBe(100.00)
        ->and($array['currency'])->toBe('EUR')
        ->and($array['locale'])->toBe('en')
        ->and($array['payment'])->toBeArray()
        ->and($array)->not->toHaveKey('billingAddress')
        ->and($array)->not->toHaveKey('lineItems');
});

test('toArray includes nested DTOs', function () {
    $request = new CreateOrderRequest(
        merchantReference: 'order-456',
        returnUrl: 'https://example.com/return',
        notificationUrl: 'https://example.com/notify',
        grandTotal: 50.00,
        currency: Currency::EUR,
        locale: Locale::ET,
        payment: new Payment(amount: 50.00, currency: Currency::EUR, method: PaymentMethodCode::CardPayments),
        billingAddress: new Address(firstName: 'John', lastName: 'Doe', country: 'EE'),
        shippingAddress: new Address(firstName: 'Jane', country: 'LT'),
        lineItems: [
            new LineItem(name: 'Widget', quantity: 2, finalPrice: 25.00),
        ],
    );

    $array = $request->toArray();
    expect($array['billingAddress']['firstName'])->toBe('John')
        ->and($array['shippingAddress']['firstName'])->toBe('Jane')
        ->and($array['lineItems'])->toHaveCount(1)
        ->and($array['lineItems'][0]['name'])->toBe('Widget');
});

test('toArray includes expiresIn and sessionUuid when set', function () {
    $request = new CreateOrderRequest(
        merchantReference: 'ref',
        returnUrl: 'https://example.com',
        notificationUrl: 'https://example.com',
        grandTotal: 10.00,
        currency: Currency::EUR,
        locale: Locale::EN,
        payment: new Payment(amount: 10.00, currency: Currency::EUR, method: PaymentMethodCode::PaymentInitiation),
        expiresIn: 600,
        sessionUuid: 'session-uuid-1',
    );

    $array = $request->toArray();
    expect($array['expiresIn'])->toBe(600)
        ->and($array['sessionUuid'])->toBe('session-uuid-1');
});

test('toArray filters null optional fields', function () {
    $request = new CreateOrderRequest(
        merchantReference: 'ref',
        returnUrl: 'https://example.com',
        notificationUrl: 'https://example.com',
        grandTotal: 10.00,
        currency: Currency::EUR,
        locale: Locale::EN,
        payment: new Payment(amount: 10.00, currency: Currency::EUR, method: PaymentMethodCode::PaymentInitiation),
    );

    $array = $request->toArray();
    expect($array)->not->toHaveKey('billingAddress')
        ->and($array)->not->toHaveKey('shippingAddress')
        ->and($array)->not->toHaveKey('lineItems')
        ->and($array)->not->toHaveKey('expiresIn')
        ->and($array)->not->toHaveKey('sessionUuid');
});

test('fromArray creates full request', function () {
    $data = [
        'merchantReference' => 'ref-full',
        'returnUrl' => 'https://return.example.com',
        'notificationUrl' => 'https://notify.example.com',
        'grandTotal' => 150.00,
        'currency' => 'EUR',
        'locale' => 'en',
        'payment' => [
            'amount' => 150.00,
            'currency' => 'EUR',
            'method' => 'paymentInitiation',
            'methodOptions' => ['preferredProvider' => 'SEB'],
        ],
        'billingAddress' => ['firstName' => 'Bill', 'country' => 'EE'],
        'shippingAddress' => ['firstName' => 'Ship', 'country' => 'LT'],
        'lineItems' => [
            ['name' => 'Item 1', 'quantity' => 1, 'finalPrice' => 100.00],
            ['name' => 'Item 2', 'quantity' => 2, 'finalPrice' => 25.00],
        ],
        'expiresIn' => 300,
        'sessionUuid' => 'sess-123',
    ];

    $request = CreateOrderRequest::fromArray($data);

    expect($request->merchantReference)->toBe('ref-full')
        ->and($request->grandTotal)->toBe(150.00)
        ->and($request->currency)->toBe(Currency::EUR)
        ->and($request->locale)->toBe(Locale::EN)
        ->and($request->payment->methodOptions->preferredProvider)->toBe('SEB')
        ->and($request->billingAddress->firstName)->toBe('Bill')
        ->and($request->shippingAddress->firstName)->toBe('Ship')
        ->and($request->lineItems)->toHaveCount(2)
        ->and($request->expiresIn)->toBe(300)
        ->and($request->sessionUuid)->toBe('sess-123');
});

test('fromArray handles minimal data', function () {
    $data = [
        'merchantReference' => 'ref',
        'returnUrl' => 'https://example.com',
        'notificationUrl' => 'https://example.com',
        'grandTotal' => 10.00,
        'currency' => 'EUR',
        'locale' => 'en',
        'payment' => ['amount' => 10.00, 'currency' => 'EUR', 'method' => 'paymentInitiation'],
    ];

    $request = CreateOrderRequest::fromArray($data);

    expect($request->billingAddress)->toBeNull()
        ->and($request->shippingAddress)->toBeNull()
        ->and($request->lineItems)->toBeNull()
        ->and($request->expiresIn)->toBeNull()
        ->and($request->sessionUuid)->toBeNull();
});

test('roundtrip toArray/fromArray', function () {
    $original = new CreateOrderRequest(
        merchantReference: 'roundtrip',
        returnUrl: 'https://example.com/return',
        notificationUrl: 'https://example.com/notify',
        grandTotal: 75.00,
        currency: Currency::PLN,
        locale: Locale::PL,
        payment: new Payment(
            amount: 75.00,
            currency: Currency::PLN,
            method: PaymentMethodCode::Blik,
            methodOptions: new \Veltix\Montonio\Payments\Dto\Request\BlikOptions(blikCode: '111222'),
        ),
        lineItems: [new LineItem(name: 'Test', quantity: 1, finalPrice: 75.00)],
    );

    $restored = CreateOrderRequest::fromArray($original->toArray());

    expect($restored->merchantReference)->toBe($original->merchantReference)
        ->and($restored->grandTotal)->toBe($original->grandTotal)
        ->and($restored->currency)->toBe($original->currency)
        ->and($restored->locale)->toBe($original->locale)
        ->and($restored->payment->method)->toBe(PaymentMethodCode::Blik)
        ->and($restored->lineItems)->toHaveCount(1)
        ->and($restored->lineItems[0]->name)->toBe('Test');
});
