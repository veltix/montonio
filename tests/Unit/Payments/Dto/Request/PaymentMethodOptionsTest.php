<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\BlikOptions;
use Veltix\Montonio\Payments\Dto\Request\BnplOptions;
use Veltix\Montonio\Payments\Dto\Request\CardPaymentOptions;
use Veltix\Montonio\Payments\Dto\Request\PaymentInitiationOptions;

test('PaymentInitiationOptions toArray filters nulls', function () {
    $opts = new PaymentInitiationOptions(preferredProvider: 'SWEDBANK', preferredCountry: 'EE');

    $array = $opts->toArray();
    expect($array)->toBe(['preferredProvider' => 'SWEDBANK', 'preferredCountry' => 'EE'])
        ->and($array)->not->toHaveKey('preferredLocale');
});

test('PaymentInitiationOptions fromArray and roundtrip', function () {
    $data = [
        'preferredProvider' => 'SEB',
        'preferredCountry' => 'LT',
        'preferredLocale' => 'lt',
        'paymentDescription' => 'Order #123',
        'paymentReference' => 'ref-456',
    ];
    $opts = PaymentInitiationOptions::fromArray($data);

    expect($opts->preferredProvider)->toBe('SEB')
        ->and($opts->paymentReference)->toBe('ref-456');

    $restored = PaymentInitiationOptions::fromArray($opts->toArray());
    expect($restored->preferredProvider)->toBe($opts->preferredProvider)
        ->and($restored->paymentDescription)->toBe($opts->paymentDescription);
});

test('CardPaymentOptions toArray filters nulls', function () {
    $opts = new CardPaymentOptions;
    expect($opts->toArray())->toBe([]);

    $opts2 = new CardPaymentOptions(preferredMethod: 'visa');
    expect($opts2->toArray())->toBe(['preferredMethod' => 'visa']);
});

test('CardPaymentOptions fromArray and roundtrip', function () {
    $opts = CardPaymentOptions::fromArray(['preferredMethod' => 'mastercard']);

    expect($opts->preferredMethod)->toBe('mastercard');

    $restored = CardPaymentOptions::fromArray($opts->toArray());
    expect($restored->preferredMethod)->toBe('mastercard');
});

test('BlikOptions toArray filters nulls', function () {
    $opts = new BlikOptions(blikCode: '123456');
    expect($opts->toArray())->toBe(['blikCode' => '123456']);
});

test('BlikOptions fromArray and roundtrip', function () {
    $opts = BlikOptions::fromArray(['preferredLocale' => 'pl', 'blikCode' => '654321']);

    expect($opts->preferredLocale)->toBe('pl')
        ->and($opts->blikCode)->toBe('654321');

    $restored = BlikOptions::fromArray($opts->toArray());
    expect($restored->blikCode)->toBe('654321');
});

test('BnplOptions toArray filters nulls', function () {
    $opts = new BnplOptions;
    expect($opts->toArray())->toBe([]);

    $opts2 = new BnplOptions(period: 12);
    expect($opts2->toArray())->toBe(['period' => 12]);
});

test('BnplOptions fromArray and roundtrip', function () {
    $opts = BnplOptions::fromArray(['period' => 6]);

    expect($opts->period)->toBe(6);

    $restored = BnplOptions::fromArray($opts->toArray());
    expect($restored->period)->toBe(6);
});
