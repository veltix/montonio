<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\BlikOptions;
use Veltix\Montonio\Payments\Dto\Request\BnplOptions;
use Veltix\Montonio\Payments\Dto\Request\CardPaymentOptions;
use Veltix\Montonio\Payments\Dto\Request\Payment;
use Veltix\Montonio\Payments\Dto\Request\PaymentInitiationOptions;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\PaymentMethodCode;

test('toArray includes required fields', function () {
    $payment = new Payment(
        amount: 100.00,
        currency: Currency::EUR,
        method: PaymentMethodCode::PaymentInitiation,
    );

    expect($payment->toArray())->toBe([
        'amount' => 100.00,
        'currency' => 'EUR',
        'method' => 'paymentInitiation',
    ]);
});

test('toArray includes methodDisplay when set', function () {
    $payment = new Payment(
        amount: 50.00,
        currency: Currency::PLN,
        method: PaymentMethodCode::Blik,
        methodDisplay: 'BLIK',
    );

    $array = $payment->toArray();
    expect($array['methodDisplay'])->toBe('BLIK');
});

test('toArray includes methodOptions when set', function () {
    $payment = new Payment(
        amount: 75.00,
        currency: Currency::EUR,
        method: PaymentMethodCode::PaymentInitiation,
        methodOptions: new PaymentInitiationOptions(preferredProvider: 'SWEDBANK'),
    );

    $array = $payment->toArray();
    expect($array['methodOptions'])->toBe(['preferredProvider' => 'SWEDBANK']);
});

test('toArray excludes null optional fields', function () {
    $payment = new Payment(amount: 10.00, currency: Currency::EUR, method: PaymentMethodCode::CardPayments);

    $array = $payment->toArray();
    expect($array)->not->toHaveKey('methodDisplay')
        ->and($array)->not->toHaveKey('methodOptions');
});

test('fromArray with PaymentInitiation options', function () {
    $payment = Payment::fromArray([
        'amount' => 100,
        'currency' => 'EUR',
        'method' => 'paymentInitiation',
        'methodOptions' => ['preferredProvider' => 'SEB'],
    ]);

    expect($payment->method)->toBe(PaymentMethodCode::PaymentInitiation)
        ->and($payment->methodOptions)->toBeInstanceOf(PaymentInitiationOptions::class)
        ->and($payment->methodOptions->preferredProvider)->toBe('SEB');
});

test('fromArray with CardPayments options', function () {
    $payment = Payment::fromArray([
        'amount' => 50,
        'currency' => 'EUR',
        'method' => 'cardPayments',
        'methodOptions' => ['preferredMethod' => 'visa'],
    ]);

    expect($payment->methodOptions)->toBeInstanceOf(CardPaymentOptions::class)
        ->and($payment->methodOptions->preferredMethod)->toBe('visa');
});

test('fromArray with Blik options', function () {
    $payment = Payment::fromArray([
        'amount' => 25,
        'currency' => 'PLN',
        'method' => 'blik',
        'methodOptions' => ['blikCode' => '123456'],
    ]);

    expect($payment->methodOptions)->toBeInstanceOf(BlikOptions::class)
        ->and($payment->methodOptions->blikCode)->toBe('123456');
});

test('fromArray with Bnpl options', function () {
    $payment = Payment::fromArray([
        'amount' => 200,
        'currency' => 'EUR',
        'method' => 'bnpl',
        'methodOptions' => ['period' => 12],
    ]);

    expect($payment->methodOptions)->toBeInstanceOf(BnplOptions::class)
        ->and($payment->methodOptions->period)->toBe(12);
});

test('roundtrip toArray/fromArray with options', function () {
    $original = new Payment(
        amount: 99.99,
        currency: Currency::EUR,
        method: PaymentMethodCode::PaymentInitiation,
        methodDisplay: 'Bank Transfer',
        methodOptions: new PaymentInitiationOptions(preferredProvider: 'SWEDBANK', preferredCountry: 'EE'),
    );

    $restored = Payment::fromArray($original->toArray());

    expect($restored->amount)->toBe($original->amount)
        ->and($restored->currency)->toBe($original->currency)
        ->and($restored->method)->toBe($original->method)
        ->and($restored->methodDisplay)->toBe($original->methodDisplay)
        ->and($restored->methodOptions)->toBeInstanceOf(PaymentInitiationOptions::class)
        ->and($restored->methodOptions->preferredProvider)->toBe('SWEDBANK');
});
