<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\BankPaymentMethod;
use Veltix\Montonio\Payments\Dto\Response\CountryPaymentMethods;
use Veltix\Montonio\Payments\Dto\Response\PaymentMethodDetail;
use Veltix\Montonio\Payments\Dto\Response\PaymentMethodsResponse;

test('fromArray maps uuid and name', function () {
    $data = [
        'uuid' => 'store-uuid-123',
        'name' => 'My Store',
        'paymentMethods' => [],
    ];

    $response = PaymentMethodsResponse::fromArray($data);

    expect($response->uuid)->toBe('store-uuid-123')
        ->and($response->name)->toBe('My Store')
        ->and($response->paymentMethods)->toBe([]);
});

test('maps payment methods by key', function () {
    $data = [
        'uuid' => 'store-1',
        'name' => 'Store',
        'paymentMethods' => [
            'paymentInitiation' => [
                'processor' => 'montonio',
                'logoUrl' => 'https://example.com/logo.png',
                'setup' => [],
            ],
            'cardPayments' => [
                'processor' => 'stripe',
            ],
        ],
    ];

    $response = PaymentMethodsResponse::fromArray($data);

    expect($response->paymentMethods)->toHaveCount(2)
        ->and($response->paymentMethods)->toHaveKey('paymentInitiation')
        ->and($response->paymentMethods)->toHaveKey('cardPayments')
        ->and($response->paymentMethods['paymentInitiation'])->toBeInstanceOf(PaymentMethodDetail::class)
        ->and($response->paymentMethods['paymentInitiation']->processor)->toBe('montonio');
});

test('maps nested setup with CountryPaymentMethods and BankPaymentMethod', function () {
    $data = [
        'uuid' => 'store-1',
        'name' => 'Store',
        'paymentMethods' => [
            'paymentInitiation' => [
                'processor' => 'montonio',
                'setup' => [
                    'EE' => [
                        'supportedCurrencies' => ['EUR'],
                        'paymentMethods' => [
                            [
                                'code' => 'SWEDBANK',
                                'name' => 'Swedbank',
                                'logoUrl' => 'https://example.com/swedbank.png',
                                'supportedCurrencies' => ['EUR'],
                                'uiPosition' => 1,
                            ],
                            [
                                'code' => 'SEB',
                                'name' => 'SEB',
                                'logoUrl' => 'https://example.com/seb.png',
                                'supportedCurrencies' => ['EUR'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $response = PaymentMethodsResponse::fromArray($data);
    $detail = $response->paymentMethods['paymentInitiation'];

    expect($detail->setup)->toHaveKey('EE');

    $country = $detail->setup['EE'];
    expect($country)->toBeInstanceOf(CountryPaymentMethods::class)
        ->and($country->supportedCurrencies)->toBe(['EUR'])
        ->and($country->paymentMethods)->toHaveCount(2);

    $bank = $country->paymentMethods[0];
    expect($bank)->toBeInstanceOf(BankPaymentMethod::class)
        ->and($bank->code)->toBe('SWEDBANK')
        ->and($bank->name)->toBe('Swedbank')
        ->and($bank->logoUrl)->toBe('https://example.com/swedbank.png')
        ->and($bank->supportedCurrencies)->toBe(['EUR'])
        ->and($bank->uiPosition)->toBe(1);

    expect($country->paymentMethods[1]->uiPosition)->toBeNull();
});

test('PaymentMethodDetail without setup', function () {
    $detail = PaymentMethodDetail::fromArray([
        'processor' => 'stripe',
    ]);

    expect($detail->processor)->toBe('stripe')
        ->and($detail->logoUrl)->toBeNull()
        ->and($detail->setup)->toBeNull();
});

test('PaymentMethodDetail with logoUrl', function () {
    $detail = PaymentMethodDetail::fromArray([
        'processor' => 'montonio',
        'logoUrl' => 'https://cdn.example.com/logo.svg',
    ]);

    expect($detail->logoUrl)->toBe('https://cdn.example.com/logo.svg');
});

test('fromArray with docs fixture maps all countries and banks', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/payment-methods.json');
    $response = PaymentMethodsResponse::fromArray($data);

    expect($response->uuid)->toBe('0bafe86b-c5cf-4c88-ba28-484a8585f0f4')
        ->and($response->name)->toBe('Montonio Store')
        ->and($response->paymentMethods)->toHaveCount(5)
        ->and($response->paymentMethods)->toHaveKey('paymentInitiation')
        ->and($response->paymentMethods)->toHaveKey('cardPayments')
        ->and($response->paymentMethods)->toHaveKey('blik')
        ->and($response->paymentMethods)->toHaveKey('bnpl')
        ->and($response->paymentMethods)->toHaveKey('hirePurchase');

    $pi = $response->paymentMethods['paymentInitiation'];
    expect($pi->processor)->toBe('montonio')
        ->and($pi->setup)->toHaveKey('EE')
        ->and($pi->setup)->toHaveKey('LT')
        ->and($pi->setup)->toHaveKey('LV')
        ->and($pi->setup)->toHaveKey('FI')
        ->and($pi->setup)->toHaveKey('PL')
        ->and($pi->setup)->toHaveKey('DE');

    $ee = $pi->setup['EE'];
    expect($ee)->toBeInstanceOf(CountryPaymentMethods::class)
        ->and($ee->supportedCurrencies)->toBe(['EUR'])
        ->and($ee->paymentMethods)->toHaveCount(8);

    $swedbank = $ee->paymentMethods[0];
    expect($swedbank)->toBeInstanceOf(BankPaymentMethod::class)
        ->and($swedbank->name)->toBe('Swedbank Estonia')
        ->and($swedbank->code)->toBe('HABAEE2X')
        ->and($swedbank->uiPosition)->toBe(1);
});
