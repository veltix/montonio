<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\CarrierShippingMethods;
use Veltix\Montonio\Shipping\Dto\Response\CountryShippingMethods;
use Veltix\Montonio\Shipping\Dto\Response\ShippingMethod;
use Veltix\Montonio\Shipping\Dto\Response\ShippingMethodConstraints;
use Veltix\Montonio\Shipping\Dto\Response\ShippingMethodsResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShippingMethodSubtype;

function shippingMethodsFixture(): array
{
    return [
        'countries' => [
            [
                'countryCode' => 'EE',
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
                                'constraints' => ['parcelDimensionsRequired' => true],
                            ],
                            [
                                'type' => 'courier',
                                'subtypes' => [
                                    ['code' => 'standard'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

test('fromArray maps countries', function () {
    $response = ShippingMethodsResponse::fromArray(shippingMethodsFixture());

    expect($response->countries)->toHaveCount(1)
        ->and($response->countries[0])->toBeInstanceOf(CountryShippingMethods::class)
        ->and($response->countries[0]->countryCode)->toBe('EE');
});

test('CountryShippingMethods maps carriers', function () {
    $response = ShippingMethodsResponse::fromArray(shippingMethodsFixture());
    $country = $response->countries[0];

    expect($country->carriers)->toHaveCount(1)
        ->and($country->carriers[0])->toBeInstanceOf(CarrierShippingMethods::class)
        ->and($country->carriers[0]->carrierCode)->toBe('omniva');
});

test('CarrierShippingMethods maps ShippingMethod', function () {
    $response = ShippingMethodsResponse::fromArray(shippingMethodsFixture());
    $methods = $response->countries[0]->carriers[0]->shippingMethods;

    expect($methods)->toHaveCount(2)
        ->and($methods[0])->toBeInstanceOf(ShippingMethod::class)
        ->and($methods[0]->type)->toBe('pickupPoint')
        ->and($methods[1]->type)->toBe('courier');
});

test('ShippingMethod maps subtypes and constraints', function () {
    $response = ShippingMethodsResponse::fromArray(shippingMethodsFixture());
    $method = $response->countries[0]->carriers[0]->shippingMethods[0];

    expect($method->subtypes)->toHaveCount(2)
        ->and($method->subtypes[0])->toBeInstanceOf(ShippingMethodSubtype::class)
        ->and($method->subtypes[0]->code)->toBe('parcelMachine')
        ->and($method->subtypes[0]->rate)->toBe(2.99)
        ->and($method->subtypes[0]->currency)->toBe('EUR')
        ->and($method->subtypes[1]->code)->toBe('postOffice')
        ->and($method->constraints)->toBeInstanceOf(ShippingMethodConstraints::class)
        ->and($method->constraints->parcelDimensionsRequired)->toBeTrue();

    $courierMethod = $response->countries[0]->carriers[0]->shippingMethods[1];
    expect($courierMethod->subtypes[0]->rate)->toBeNull()
        ->and($courierMethod->constraints)->toBeNull();
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/shipping-methods.json');
    $response = ShippingMethodsResponse::fromArray($data);

    expect($response->countries)->toHaveCount(1)
        ->and($response->countries[0]->countryCode)->toBe('EE')
        ->and($response->countries[0]->carriers)->toHaveCount(2);

    $dpd = $response->countries[0]->carriers[0];
    expect($dpd->carrierCode)->toBe('dpd')
        ->and($dpd->shippingMethods)->toHaveCount(2)
        ->and($dpd->shippingMethods[0]->type)->toBe('courier')
        ->and($dpd->shippingMethods[0]->constraints->parcelDimensionsRequired)->toBeFalse()
        ->and($dpd->shippingMethods[1]->type)->toBe('pickupPoint')
        ->and($dpd->shippingMethods[1]->subtypes)->toHaveCount(2)
        ->and($dpd->shippingMethods[1]->subtypes[0]->code)->toBe('parcelMachine')
        ->and($dpd->shippingMethods[1]->subtypes[1]->code)->toBe('parcelShop');

    $omniva = $response->countries[0]->carriers[1];
    expect($omniva->carrierCode)->toBe('omniva')
        ->and($omniva->shippingMethods[1]->subtypes[1]->code)->toBe('postOffice');
});
