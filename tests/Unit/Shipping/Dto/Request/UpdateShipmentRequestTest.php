<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\ShipmentParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentReceiver;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentShippingMethod;
use Veltix\Montonio\Shipping\Dto\Request\UpdateShipmentRequest;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;

test('toArray is empty when all null', function () {
    $request = new UpdateShipmentRequest;

    expect($request->toArray())->toBe([]);
});

test('toArray includes only non-null fields', function () {
    $request = new UpdateShipmentRequest(
        receiver: new ShipmentReceiver(name: 'Updated', phoneCountryCode: '+372', phoneNumber: '999'),
        parcels: [new ShipmentParcel(weight: 3.0)],
    );

    $array = $request->toArray();
    expect($array)->toHaveKey('receiver')
        ->and($array)->toHaveKey('parcels')
        ->and($array)->not->toHaveKey('shippingMethod')
        ->and($array)->not->toHaveKey('sender')
        ->and($array['receiver']['name'])->toBe('Updated');
});

test('roundtrip toArray/fromArray', function () {
    $original = new UpdateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'c-new'),
        receiver: new ShipmentReceiver(name: 'New Receiver', phoneCountryCode: '+1', phoneNumber: '555'),
    );

    $restored = UpdateShipmentRequest::fromArray($original->toArray());

    expect($restored->shippingMethod->id)->toBe('c-new')
        ->and($restored->receiver->name)->toBe('New Receiver')
        ->and($restored->sender)->toBeNull()
        ->and($restored->parcels)->toBeNull();
});
