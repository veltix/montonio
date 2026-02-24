<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\Payout;
use Veltix\Montonio\Payments\Dto\Response\PayoutsResponse;

function payoutFixture(): array
{
    return [
        'uuid' => 'payout-uuid-1',
        'storeUuid' => 'store-uuid-1',
        'storeName' => 'My Store',
        'storeLegalName' => 'My Store OÜ',
        'iban' => 'EE123456789012345678',
        'accountName' => 'My Store OÜ',
        'status' => 'completed',
        'settlementType' => 'GROSS',
        'paymentsAmount' => '1000.00',
        'refundsAmount' => '50.00',
        'totalAmount' => '950.00',
        'currency' => 'EUR',
        'expectedArrivalDate' => '2025-01-20',
        'createdAt' => '2025-01-15T10:00:00Z',
    ];
}

test('fromArray maps Payout array from payouts key', function () {
    $response = PayoutsResponse::fromArray([
        'payouts' => [payoutFixture()],
    ]);

    expect($response->payouts)->toHaveCount(1)
        ->and($response->payouts[0])->toBeInstanceOf(Payout::class);
});

test('fromArray maps Payout array from flat data', function () {
    $response = PayoutsResponse::fromArray([payoutFixture()]);

    expect($response->payouts)->toHaveCount(1);
});

test('Payout has all 14 fields', function () {
    $payout = Payout::fromArray(payoutFixture());

    expect($payout->uuid)->toBe('payout-uuid-1')
        ->and($payout->storeUuid)->toBe('store-uuid-1')
        ->and($payout->storeName)->toBe('My Store')
        ->and($payout->storeLegalName)->toBe('My Store OÜ')
        ->and($payout->iban)->toBe('EE123456789012345678')
        ->and($payout->accountName)->toBe('My Store OÜ')
        ->and($payout->status)->toBe('completed')
        ->and($payout->settlementType)->toBe('GROSS')
        ->and($payout->paymentsAmount)->toBe('1000.00')
        ->and($payout->refundsAmount)->toBe('50.00')
        ->and($payout->totalAmount)->toBe('950.00')
        ->and($payout->currency)->toBe('EUR')
        ->and($payout->expectedArrivalDate)->toBe('2025-01-20')
        ->and($payout->createdAt)->toBe('2025-01-15T10:00:00Z');
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/payouts.json');
    $response = PayoutsResponse::fromArray($data);

    expect($response->payouts)->toHaveCount(1);

    $payout = $response->payouts[0];
    expect($payout->uuid)->toBe('671d9d42-7751-4a52-8734-d6eb250c3eea')
        ->and($payout->storeUuid)->toBe('b9ae2ec0-641c-421d-9a78-202bd59614d9')
        ->and($payout->storeName)->toBe('Shared Dev Store')
        ->and($payout->storeLegalName)->toBe('Shared Dev Store LLC')
        ->and($payout->iban)->toBe('MK88001061647941675')
        ->and($payout->status)->toBe('SUCCESSFUL')
        ->and($payout->settlementType)->toBe('montonioMoneyMovement')
        ->and($payout->paymentsAmount)->toBe('43333.00')
        ->and($payout->refundsAmount)->toBe('32500.00')
        ->and($payout->totalAmount)->toBe('130000.00')
        ->and($payout->expectedArrivalDate)->toBeNull();
});
