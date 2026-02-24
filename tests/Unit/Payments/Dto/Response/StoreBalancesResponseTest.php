<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\BalanceEntry;
use Veltix\Montonio\Payments\Dto\Response\StoreBalancesResponse;
use Veltix\Montonio\Payments\Dto\Response\StoreInfo;

test('fromArray maps StoreInfo', function () {
    $response = StoreBalancesResponse::fromArray([
        'store' => ['uuid' => 'store-1', 'name' => 'My Store', 'legalName' => 'My Store OÜ'],
        'balances' => [],
    ]);

    expect($response->store)->toBeInstanceOf(StoreInfo::class)
        ->and($response->store->uuid)->toBe('store-1')
        ->and($response->store->name)->toBe('My Store')
        ->and($response->store->legalName)->toBe('My Store OÜ');
});

test('fromArray maps keyed balances', function () {
    $response = StoreBalancesResponse::fromArray([
        'store' => ['uuid' => 's', 'name' => 'S', 'legalName' => 'S OÜ'],
        'balances' => [
            'paymentInitiation' => [
                ['currency' => 'EUR', 'balance' => 100.50],
                ['currency' => 'PLN', 'balance' => 200.00],
            ],
            'cardPayments' => [
                ['currency' => 'EUR', 'balance' => 50.25],
            ],
        ],
    ]);

    expect($response->balances)->toHaveCount(2)
        ->and($response->balances)->toHaveKey('paymentInitiation')
        ->and($response->balances)->toHaveKey('cardPayments')
        ->and($response->balances['paymentInitiation'])->toHaveCount(2)
        ->and($response->balances['cardPayments'])->toHaveCount(1);
});

test('BalanceEntry has correct types', function () {
    $entry = BalanceEntry::fromArray(['currency' => 'EUR', 'balance' => '123.45']);

    expect($entry)->toBeInstanceOf(BalanceEntry::class)
        ->and($entry->currency)->toBe('EUR')
        ->and($entry->balance)->toBe(123.45)
        ->and($entry->balance)->toBeFloat();
});

test('balance values are cast to float', function () {
    $response = StoreBalancesResponse::fromArray([
        'store' => ['uuid' => 's', 'name' => 'S', 'legalName' => 'S'],
        'balances' => [
            'test' => [
                ['currency' => 'EUR', 'balance' => '99'],
            ],
        ],
    ]);

    expect($response->balances['test'][0]->balance)->toBe(99.0)
        ->and($response->balances['test'][0]->balance)->toBeFloat();
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/store-balances.json');
    $response = StoreBalancesResponse::fromArray($data);

    expect($response->store->uuid)->toBe('74e498c8-8d80-4b79-a4ba-ae2c12bbe50d')
        ->and($response->store->name)->toBe('ShopName')
        ->and($response->store->legalName)->toBe('Shop legal name LLC')
        ->and($response->balances)->toHaveCount(2)
        ->and($response->balances)->toHaveKey('stripe')
        ->and($response->balances)->toHaveKey('montonioMoneyMovement')
        ->and($response->balances['stripe'])->toHaveCount(1)
        ->and($response->balances['stripe'][0]->currency)->toBe('EUR')
        ->and($response->balances['stripe'][0]->balance)->toBe(0.0)
        ->and($response->balances['montonioMoneyMovement'])->toHaveCount(2)
        ->and($response->balances['montonioMoneyMovement'][0]->balance)->toBe(33.0)
        ->and($response->balances['montonioMoneyMovement'][1]->currency)->toBe('PLN')
        ->and($response->balances['montonioMoneyMovement'][1]->balance)->toBe(280.0);
});
