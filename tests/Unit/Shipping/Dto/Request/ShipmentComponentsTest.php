<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\ShipmentParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentProduct;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentReceiver;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentSender;

test('ShipmentSender toArray filters nulls', function () {
    $sender = new ShipmentSender(name: 'Warehouse', phoneCountryCode: '+372', phoneNumber: '123');

    $array = $sender->toArray();
    expect($array)->toBe(['name' => 'Warehouse', 'phoneCountryCode' => '+372', 'phoneNumber' => '123'])
        ->and($array)->not->toHaveKey('streetAddress');
});

test('ShipmentSender fromArray and roundtrip', function () {
    $data = [
        'name' => 'Sender',
        'phoneCountryCode' => '+370',
        'phoneNumber' => '600',
        'streetAddress' => '1 Vilnius St',
        'locality' => 'Vilnius',
        'postalCode' => '01001',
        'country' => 'LT',
        'email' => 'sender@example.com',
    ];

    $sender = ShipmentSender::fromArray($data);
    expect($sender->name)->toBe('Sender')
        ->and($sender->streetAddress)->toBe('1 Vilnius St')
        ->and($sender->email)->toBe('sender@example.com')
        ->and($sender->region)->toBeNull();

    $restored = ShipmentSender::fromArray($sender->toArray());
    expect($restored->name)->toBe($sender->name)
        ->and($restored->email)->toBe($sender->email);
});

test('ShipmentReceiver toArray filters nulls', function () {
    $receiver = new ShipmentReceiver(name: 'John', phoneCountryCode: '+372', phoneNumber: '555');

    $array = $receiver->toArray();
    expect($array)->toBe(['name' => 'John', 'phoneCountryCode' => '+372', 'phoneNumber' => '555']);
});

test('ShipmentReceiver fromArray and roundtrip', function () {
    $data = [
        'name' => 'John Doe',
        'phoneCountryCode' => '+372',
        'phoneNumber' => '5551234',
        'firstName' => 'John',
        'lastName' => 'Doe',
        'streetAddress' => '123 Main St',
        'locality' => 'Tallinn',
        'postalCode' => '10115',
        'country' => 'EE',
    ];

    $receiver = ShipmentReceiver::fromArray($data);
    expect($receiver->firstName)->toBe('John')
        ->and($receiver->streetAddress)->toBe('123 Main St');

    $restored = ShipmentReceiver::fromArray($receiver->toArray());
    expect($restored->firstName)->toBe($receiver->firstName);
});

test('ShipmentParcel toArray filters null dimensions', function () {
    $parcel = new ShipmentParcel(weight: 1.5);

    expect($parcel->toArray())->toBe(['weight' => 1.5]);

    $full = new ShipmentParcel(weight: 2.0, height: 10.0, width: 20.0, length: 30.0);
    expect($full->toArray())->toBe(['weight' => 2.0, 'height' => 10.0, 'width' => 20.0, 'length' => 30.0]);
});

test('ShipmentParcel fromArray and roundtrip', function () {
    $parcel = ShipmentParcel::fromArray(['weight' => 3.0, 'height' => 5.0]);

    expect($parcel->weight)->toBe(3.0)
        ->and($parcel->height)->toBe(5.0)
        ->and($parcel->width)->toBeNull();

    $restored = ShipmentParcel::fromArray($parcel->toArray());
    expect($restored->weight)->toBe(3.0)
        ->and($restored->height)->toBe(5.0);
});

test('ShipmentProduct toArray includes required and optional fields', function () {
    $product = new ShipmentProduct(
        sku: 'SKU-001',
        name: 'Widget',
        quantity: 3,
        price: 9.99,
        currency: 'EUR',
    );

    $array = $product->toArray();
    expect($array['sku'])->toBe('SKU-001')
        ->and($array['name'])->toBe('Widget')
        ->and($array['quantity'])->toBe(3)
        ->and($array['price'])->toBe(9.99)
        ->and($array['currency'])->toBe('EUR')
        ->and($array)->not->toHaveKey('barcode');
});

test('ShipmentProduct fromArray and roundtrip', function () {
    $data = [
        'sku' => 'SKU-002',
        'name' => 'Gadget',
        'quantity' => 1,
        'barcode' => '1234567890',
        'price' => 29.99,
        'currency' => 'EUR',
        'attributes' => ['color' => 'red'],
        'imageUrl' => 'https://example.com/img.png',
        'storeProductUrl' => 'https://example.com/product',
        'description' => 'A great gadget',
    ];

    $product = ShipmentProduct::fromArray($data);
    expect($product->barcode)->toBe('1234567890')
        ->and($product->attributes)->toBe(['color' => 'red'])
        ->and($product->description)->toBe('A great gadget');

    $restored = ShipmentProduct::fromArray($product->toArray());
    expect($restored->sku)->toBe($product->sku)
        ->and($restored->barcode)->toBe($product->barcode);
});
