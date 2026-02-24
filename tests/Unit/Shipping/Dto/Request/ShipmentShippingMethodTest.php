<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\AdditionalService;
use Veltix\Montonio\Shipping\Dto\Request\CodParams;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentShippingMethod;
use Veltix\Montonio\Shipping\Enum\AdditionalServiceCode;
use Veltix\Montonio\Shipping\Enum\LockerSize;
use Veltix\Montonio\Shipping\Enum\ParcelHandoverMethod;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;

test('toArray includes type and id', function () {
    $method = new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'courier-ee-1');

    expect($method->toArray())->toBe(['type' => 'courier', 'id' => 'courier-ee-1']);
});

test('toArray includes additionalServices with CodParams', function () {
    $method = new ShipmentShippingMethod(
        type: ShippingMethodType::Courier,
        id: 'courier-1',
        additionalServices: [
            new AdditionalService(
                code: AdditionalServiceCode::Cod,
                params: new CodParams(amount: 15.99),
            ),
            new AdditionalService(code: AdditionalServiceCode::AgeVerification),
        ],
    );

    $array = $method->toArray();
    expect($array['additionalServices'])->toHaveCount(2)
        ->and($array['additionalServices'][0]['code'])->toBe('cod')
        ->and($array['additionalServices'][0]['params']['amount'])->toBe(15.99)
        ->and($array['additionalServices'][1]['code'])->toBe('ageVerification')
        ->and($array['additionalServices'][1])->not->toHaveKey('params');
});

test('toArray includes handover and locker enums', function () {
    $method = new ShipmentShippingMethod(
        type: ShippingMethodType::PickupPoint,
        id: 'pp-1',
        parcelHandoverMethod: ParcelHandoverMethod::CourierPickUp,
        lockerSize: LockerSize::M,
    );

    $array = $method->toArray();
    expect($array['parcelHandoverMethod'])->toBe('courierPickUp')
        ->and($array['lockerSize'])->toBe('M');
});

test('toArray excludes null optional fields', function () {
    $method = new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'c-1');

    $array = $method->toArray();
    expect($array)->not->toHaveKey('additionalServices')
        ->and($array)->not->toHaveKey('parcelHandoverMethod')
        ->and($array)->not->toHaveKey('lockerSize');
});

test('fromArray creates full method', function () {
    $data = [
        'type' => 'pickupPoint',
        'id' => 'pp-omniva-ee-123',
        'additionalServices' => [
            ['code' => 'cod', 'params' => ['amount' => 25.00]],
        ],
        'parcelHandoverMethod' => 'terminalDropOff',
        'lockerSize' => 'L',
    ];

    $method = ShipmentShippingMethod::fromArray($data);

    expect($method->type)->toBe(ShippingMethodType::PickupPoint)
        ->and($method->id)->toBe('pp-omniva-ee-123')
        ->and($method->additionalServices)->toHaveCount(1)
        ->and($method->additionalServices[0]->code)->toBe(AdditionalServiceCode::Cod)
        ->and($method->additionalServices[0]->params->amount)->toBe(25.00)
        ->and($method->parcelHandoverMethod)->toBe(ParcelHandoverMethod::TerminalDropOff)
        ->and($method->lockerSize)->toBe(LockerSize::L);
});

test('roundtrip toArray/fromArray', function () {
    $original = new ShipmentShippingMethod(
        type: ShippingMethodType::PickupPoint,
        id: 'pp-1',
        additionalServices: [new AdditionalService(code: AdditionalServiceCode::AgeVerification)],
        parcelHandoverMethod: ParcelHandoverMethod::CourierPickUp,
        lockerSize: LockerSize::XS,
    );

    $restored = ShipmentShippingMethod::fromArray($original->toArray());

    expect($restored->type)->toBe($original->type)
        ->and($restored->id)->toBe($original->id)
        ->and($restored->additionalServices)->toHaveCount(1)
        ->and($restored->additionalServices[0]->code)->toBe(AdditionalServiceCode::AgeVerification)
        ->and($restored->parcelHandoverMethod)->toBe(ParcelHandoverMethod::CourierPickUp)
        ->and($restored->lockerSize)->toBe(LockerSize::XS);
});
