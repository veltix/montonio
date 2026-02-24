<?php

declare(strict_types=1);

use Veltix\Montonio\Environment;

test('production has correct value', function () {
    expect(Environment::Production->value)->toBe('production');
});

test('sandbox has correct value', function () {
    expect(Environment::Sandbox->value)->toBe('sandbox');
});

test('production payments base url', function () {
    expect(Environment::Production->paymentsBaseUrl())
        ->toBe('https://stargate.montonio.com/api');
});

test('sandbox payments base url', function () {
    expect(Environment::Sandbox->paymentsBaseUrl())
        ->toBe('https://sandbox-stargate.montonio.com/api');
});

test('production shipping base url', function () {
    expect(Environment::Production->shippingBaseUrl())
        ->toBe('https://shipping.montonio.com/api/v2');
});

test('sandbox shipping base url', function () {
    expect(Environment::Sandbox->shippingBaseUrl())
        ->toBe('https://sandbox-shipping.montonio.com/api/v2');
});
