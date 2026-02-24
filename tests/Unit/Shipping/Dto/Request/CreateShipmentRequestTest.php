<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\CreateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentProduct;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentReceiver;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentSender;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentShippingMethod;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;

test('toArray includes required nested fields', function () {
    $request = new CreateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(
            type: ShippingMethodType::PickupPoint,
            id: 'pm-omniva-ee-123',
        ),
        receiver: new ShipmentReceiver(
            name: 'John Doe',
            phoneCountryCode: '+372',
            phoneNumber: '5551234',
        ),
        parcels: [new ShipmentParcel(weight: 1.5)],
    );

    $array = $request->toArray();
    expect($array['shippingMethod']['type'])->toBe('pickupPoint')
        ->and($array['shippingMethod']['id'])->toBe('pm-omniva-ee-123')
        ->and($array['receiver']['name'])->toBe('John Doe')
        ->and($array['parcels'])->toHaveCount(1)
        ->and($array['parcels'][0]['weight'])->toBe(1.5);
});

test('toArray includes optional fields when set', function () {
    $request = new CreateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'c-1'),
        receiver: new ShipmentReceiver(name: 'Jane', phoneCountryCode: '+370', phoneNumber: '600123'),
        parcels: [new ShipmentParcel(weight: 2.0)],
        sender: new ShipmentSender(name: 'Warehouse', phoneCountryCode: '+372', phoneNumber: '1234567'),
        merchantReference: 'order-456',
        montonioOrderUuid: 'mont-uuid-1',
        orderComment: 'Fragile',
        products: [new ShipmentProduct(sku: 'SKU-1', name: 'Widget', quantity: 2)],
        synchronous: true,
    );

    $array = $request->toArray();
    expect($array)->toHaveKey('sender')
        ->and($array['sender']['name'])->toBe('Warehouse')
        ->and($array['merchantReference'])->toBe('order-456')
        ->and($array['montonioOrderUuid'])->toBe('mont-uuid-1')
        ->and($array['orderComment'])->toBe('Fragile')
        ->and($array['products'])->toHaveCount(1)
        ->and($array['products'][0]['sku'])->toBe('SKU-1')
        ->and($array['synchronous'])->toBeTrue();
});

test('toArray filters null optional fields', function () {
    $request = new CreateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'c-1'),
        receiver: new ShipmentReceiver(name: 'Test', phoneCountryCode: '+1', phoneNumber: '555'),
        parcels: [new ShipmentParcel(weight: 1.0)],
    );

    $array = $request->toArray();
    expect($array)->not->toHaveKey('sender')
        ->and($array)->not->toHaveKey('merchantReference')
        ->and($array)->not->toHaveKey('montonioOrderUuid')
        ->and($array)->not->toHaveKey('orderComment')
        ->and($array)->not->toHaveKey('products')
        ->and($array)->not->toHaveKey('synchronous');
});

test('fromArray creates full request', function () {
    $data = [
        'shippingMethod' => ['type' => 'pickupPoint', 'id' => 'pp-1'],
        'receiver' => [
            'name' => 'John Doe',
            'phoneCountryCode' => '+372',
            'phoneNumber' => '5551234',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'streetAddress' => '123 Main St',
            'locality' => 'Tallinn',
            'postalCode' => '10115',
            'country' => 'EE',
        ],
        'parcels' => [
            ['weight' => 1.5, 'height' => 10.0, 'width' => 20.0, 'length' => 30.0],
        ],
        'sender' => [
            'name' => 'Warehouse',
            'phoneCountryCode' => '+372',
            'phoneNumber' => '1234567',
        ],
        'merchantReference' => 'order-ref',
        'products' => [
            ['sku' => 'SKU1', 'name' => 'Product 1', 'quantity' => 3, 'price' => 9.99, 'currency' => 'EUR'],
        ],
        'synchronous' => true,
    ];

    $request = CreateShipmentRequest::fromArray($data);

    expect($request->shippingMethod->type)->toBe(ShippingMethodType::PickupPoint)
        ->and($request->receiver->name)->toBe('John Doe')
        ->and($request->receiver->email)->toBe('john@example.com')
        ->and($request->parcels)->toHaveCount(1)
        ->and($request->parcels[0]->height)->toBe(10.0)
        ->and($request->sender->name)->toBe('Warehouse')
        ->and($request->merchantReference)->toBe('order-ref')
        ->and($request->products)->toHaveCount(1)
        ->and($request->products[0]->price)->toBe(9.99)
        ->and($request->synchronous)->toBeTrue();
});

test('fromArray handles minimal data', function () {
    $data = [
        'shippingMethod' => ['type' => 'courier', 'id' => 'c-1'],
        'receiver' => ['name' => 'Jane', 'phoneCountryCode' => '+1', 'phoneNumber' => '555'],
        'parcels' => [['weight' => 0.5]],
    ];

    $request = CreateShipmentRequest::fromArray($data);

    expect($request->sender)->toBeNull()
        ->and($request->merchantReference)->toBeNull()
        ->and($request->products)->toBeNull()
        ->and($request->synchronous)->toBeNull();
});

test('roundtrip toArray/fromArray', function () {
    $original = new CreateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'courier-1'),
        receiver: new ShipmentReceiver(name: 'Test User', phoneCountryCode: '+372', phoneNumber: '5556789', email: 'test@test.com'),
        parcels: [
            new ShipmentParcel(weight: 2.5, height: 15.0, width: 25.0, length: 35.0),
        ],
        merchantReference: 'roundtrip-ref',
    );

    $restored = CreateShipmentRequest::fromArray($original->toArray());

    expect($restored->shippingMethod->type)->toBe($original->shippingMethod->type)
        ->and($restored->shippingMethod->id)->toBe($original->shippingMethod->id)
        ->and($restored->receiver->name)->toBe($original->receiver->name)
        ->and($restored->receiver->email)->toBe('test@test.com')
        ->and($restored->parcels)->toHaveCount(1)
        ->and($restored->parcels[0]->weight)->toBe(2.5)
        ->and($restored->merchantReference)->toBe('roundtrip-ref');
});
