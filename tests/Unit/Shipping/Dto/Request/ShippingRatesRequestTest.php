<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\RatesItem;
use Veltix\Montonio\Shipping\Dto\Request\RatesParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShippingRatesRequest;
use Veltix\Montonio\Shipping\Enum\DimensionUnit;
use Veltix\Montonio\Shipping\Enum\WeightUnit;

test('toArray includes destination and parcels', function () {
    $request = new ShippingRatesRequest(
        destination: 'EE',
        parcels: [
            new RatesParcel(
                items: [
                    new RatesItem(length: 30.0, width: 20.0, height: 10.0, weight: 1.5),
                ],
            ),
        ],
    );

    $array = $request->toArray();
    expect($array['destination'])->toBe('EE')
        ->and($array['parcels'])->toHaveCount(1)
        ->and($array['parcels'][0]['items'])->toHaveCount(1)
        ->and($array['parcels'][0]['items'][0]['length'])->toBe(30.0);
});

test('RatesItem includes optional enums', function () {
    $item = new RatesItem(
        length: 10.0,
        width: 10.0,
        height: 10.0,
        weight: 0.5,
        dimensionUnit: DimensionUnit::Cm,
        weightUnit: WeightUnit::Kg,
        quantity: 2,
    );

    $array = $item->toArray();
    expect($array['dimensionUnit'])->toBe('cm')
        ->and($array['weightUnit'])->toBe('kg')
        ->and($array['quantity'])->toBe(2);
});

test('RatesItem excludes null optional fields', function () {
    $item = new RatesItem(length: 10.0, width: 10.0, height: 10.0, weight: 0.5);

    $array = $item->toArray();
    expect($array)->not->toHaveKey('dimensionUnit')
        ->and($array)->not->toHaveKey('weightUnit')
        ->and($array)->not->toHaveKey('quantity');
});

test('roundtrip toArray/fromArray', function () {
    $original = new ShippingRatesRequest(
        destination: 'LT',
        parcels: [
            new RatesParcel(
                items: [
                    new RatesItem(
                        length: 20.0,
                        width: 15.0,
                        height: 10.0,
                        weight: 2.0,
                        dimensionUnit: DimensionUnit::M,
                        weightUnit: WeightUnit::G,
                        quantity: 3,
                    ),
                ],
            ),
        ],
    );

    $restored = ShippingRatesRequest::fromArray($original->toArray());

    expect($restored->destination)->toBe('LT')
        ->and($restored->parcels)->toHaveCount(1)
        ->and($restored->parcels[0]->items[0]->dimensionUnit)->toBe(DimensionUnit::M)
        ->and($restored->parcels[0]->items[0]->weightUnit)->toBe(WeightUnit::G)
        ->and($restored->parcels[0]->items[0]->quantity)->toBe(3);
});
