<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Enum\PaymentStatus;

test('has 7 cases', function () {
    expect(PaymentStatus::cases())->toHaveCount(7);
});

test('has correct values', function () {
    expect(PaymentStatus::PENDING->value)->toBe('PENDING')
        ->and(PaymentStatus::PAID->value)->toBe('PAID')
        ->and(PaymentStatus::VOIDED->value)->toBe('VOIDED')
        ->and(PaymentStatus::PARTIALLY_REFUNDED->value)->toBe('PARTIALLY_REFUNDED')
        ->and(PaymentStatus::REFUNDED->value)->toBe('REFUNDED')
        ->and(PaymentStatus::ABANDONED->value)->toBe('ABANDONED')
        ->and(PaymentStatus::AUTHORIZED->value)->toBe('AUTHORIZED');
});

test('from() works for all values', function () {
    expect(PaymentStatus::from('PENDING'))->toBe(PaymentStatus::PENDING)
        ->and(PaymentStatus::from('PAID'))->toBe(PaymentStatus::PAID)
        ->and(PaymentStatus::from('VOIDED'))->toBe(PaymentStatus::VOIDED)
        ->and(PaymentStatus::from('PARTIALLY_REFUNDED'))->toBe(PaymentStatus::PARTIALLY_REFUNDED)
        ->and(PaymentStatus::from('REFUNDED'))->toBe(PaymentStatus::REFUNDED)
        ->and(PaymentStatus::from('ABANDONED'))->toBe(PaymentStatus::ABANDONED)
        ->and(PaymentStatus::from('AUTHORIZED'))->toBe(PaymentStatus::AUTHORIZED);
});
