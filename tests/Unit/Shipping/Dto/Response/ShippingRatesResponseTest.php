<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\CarrierRates;
use Veltix\Montonio\Shipping\Dto\Response\CarrierShippingMethodRate;
use Veltix\Montonio\Shipping\Dto\Response\EstimatedParcel;
use Veltix\Montonio\Shipping\Dto\Response\RateCalculationDetails;
use Veltix\Montonio\Shipping\Dto\Response\RateSubtype;
use Veltix\Montonio\Shipping\Dto\Response\ShippingRatesResponse;

function ratesFixture(): array
{
    return [
        'calculationDetails' => [
            'estimatedParcels' => [
                [
                    'length' => 30.0,
                    'width' => 20.0,
                    'height' => 10.0,
                    'dimensionUnit' => 'cm',
                    'actualWeight' => 1.5,
                    'volumetricWeight' => 1.0,
                    'chargeableWeight' => 1.5,
                    'weightUnit' => 'kg',
                    'bufferApplied' => false,
                ],
            ],
        ],
        'destination' => 'EE',
        'carriers' => [
            [
                'carrierCode' => 'omniva',
                'shippingMethods' => [
                    [
                        'type' => 'pickupPoint',
                        'subtypes' => [
                            ['code' => 'parcelMachine', 'rate' => 2.99, 'currency' => 'EUR'],
                            ['code' => 'postOffice', 'rate' => 3.49, 'currency' => 'EUR'],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

test('fromArray maps calculationDetails', function () {
    $response = ShippingRatesResponse::fromArray(ratesFixture());

    expect($response->calculationDetails)->toBeInstanceOf(RateCalculationDetails::class)
        ->and($response->destination)->toBe('EE');
});

test('EstimatedParcel maps all fields', function () {
    $response = ShippingRatesResponse::fromArray(ratesFixture());
    $parcel = $response->calculationDetails->estimatedParcels[0];

    expect($parcel)->toBeInstanceOf(EstimatedParcel::class)
        ->and($parcel->length)->toBe(30.0)
        ->and($parcel->width)->toBe(20.0)
        ->and($parcel->height)->toBe(10.0)
        ->and($parcel->dimensionUnit)->toBe('cm')
        ->and($parcel->actualWeight)->toBe(1.5)
        ->and($parcel->volumetricWeight)->toBe(1.0)
        ->and($parcel->chargeableWeight)->toBe(1.5)
        ->and($parcel->weightUnit)->toBe('kg')
        ->and($parcel->bufferApplied)->toBeFalse();
});

test('carriers chain maps correctly', function () {
    $response = ShippingRatesResponse::fromArray(ratesFixture());

    expect($response->carriers)->toHaveCount(1)
        ->and($response->carriers[0])->toBeInstanceOf(CarrierRates::class)
        ->and($response->carriers[0]->carrierCode)->toBe('omniva')
        ->and($response->carriers[0]->shippingMethods)->toHaveCount(1)
        ->and($response->carriers[0]->shippingMethods[0])->toBeInstanceOf(CarrierShippingMethodRate::class)
        ->and($response->carriers[0]->shippingMethods[0]->type)->toBe('pickupPoint');
});

test('RateSubtype maps with float cast', function () {
    $response = ShippingRatesResponse::fromArray(ratesFixture());
    $subtypes = $response->carriers[0]->shippingMethods[0]->subtypes;

    expect($subtypes)->toHaveCount(2)
        ->and($subtypes[0])->toBeInstanceOf(RateSubtype::class)
        ->and($subtypes[0]->code)->toBe('parcelMachine')
        ->and($subtypes[0]->rate)->toBe(2.99)
        ->and($subtypes[0]->rate)->toBeFloat()
        ->and($subtypes[0]->currency)->toBe('EUR');
});

test('RateSubtype handles string rate values', function () {
    $subtype = RateSubtype::fromArray(['code' => 'test', 'rate' => '5', 'currency' => 'PLN']);

    expect($subtype->rate)->toBe(5.0)
        ->and($subtype->rate)->toBeFloat();
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/shipping-rates.json');
    $response = ShippingRatesResponse::fromArray($data);

    expect($response->destination)->toBe('EE')
        ->and($response->calculationDetails->estimatedParcels)->toHaveCount(1);

    $parcel = $response->calculationDetails->estimatedParcels[0];
    expect($parcel->length)->toBe(20.0)
        ->and($parcel->width)->toBe(15.0)
        ->and($parcel->height)->toBe(10.0)
        ->and($parcel->dimensionUnit)->toBe('cm')
        ->and($parcel->actualWeight)->toBe(0.5)
        ->and($parcel->volumetricWeight)->toBe(0.75)
        ->and($parcel->chargeableWeight)->toBe(0.75);

    expect($response->carriers)->toHaveCount(2);

    $omniva = $response->carriers[0];
    expect($omniva->carrierCode)->toBe('omniva')
        ->and($omniva->shippingMethods[0]->type)->toBe('pickupPoint')
        ->and($omniva->shippingMethods[0]->subtypes)->toHaveCount(2)
        ->and($omniva->shippingMethods[0]->subtypes[0]->code)->toBe('parcelMachine')
        ->and($omniva->shippingMethods[0]->subtypes[0]->rate)->toBe(2.50)
        ->and($omniva->shippingMethods[0]->subtypes[0]->currency)->toBe('EUR');

    $dpd = $response->carriers[1];
    expect($dpd->carrierCode)->toBe('dpd')
        ->and($dpd->shippingMethods[0]->subtypes[0]->rate)->toBe(3.0);
});
