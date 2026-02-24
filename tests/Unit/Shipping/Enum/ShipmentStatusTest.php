<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Enum\ShipmentStatus;

test('has 8 cases', function () {
    expect(ShipmentStatus::cases())->toHaveCount(8);
});

test('has correct values', function () {
    expect(ShipmentStatus::Pending->value)->toBe('pending')
        ->and(ShipmentStatus::Registered->value)->toBe('registered')
        ->and(ShipmentStatus::RegistrationFailed->value)->toBe('registrationFailed')
        ->and(ShipmentStatus::LabelsCreated->value)->toBe('labelsCreated')
        ->and(ShipmentStatus::InTransit->value)->toBe('inTransit')
        ->and(ShipmentStatus::AwaitingCollection->value)->toBe('awaitingCollection')
        ->and(ShipmentStatus::Delivered->value)->toBe('delivered')
        ->and(ShipmentStatus::Returned->value)->toBe('returned');
});

test('from() works for all values', function () {
    expect(ShipmentStatus::from('pending'))->toBe(ShipmentStatus::Pending)
        ->and(ShipmentStatus::from('registered'))->toBe(ShipmentStatus::Registered)
        ->and(ShipmentStatus::from('registrationFailed'))->toBe(ShipmentStatus::RegistrationFailed)
        ->and(ShipmentStatus::from('labelsCreated'))->toBe(ShipmentStatus::LabelsCreated)
        ->and(ShipmentStatus::from('inTransit'))->toBe(ShipmentStatus::InTransit)
        ->and(ShipmentStatus::from('awaitingCollection'))->toBe(ShipmentStatus::AwaitingCollection)
        ->and(ShipmentStatus::from('delivered'))->toBe(ShipmentStatus::Delivered)
        ->and(ShipmentStatus::from('returned'))->toBe(ShipmentStatus::Returned);
});
