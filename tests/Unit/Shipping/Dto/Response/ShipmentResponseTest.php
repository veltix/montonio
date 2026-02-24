<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\ShipmentParcelResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentProductResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentReceiverResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentSenderResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentShippingMethodResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentStoreResponse;
use Veltix\Montonio\Shipping\Enum\ShipmentStatus;

function shipmentFixture(): array
{
    return [
        'id' => 'ship-uuid-123',
        'createdAt' => '2025-01-15T10:00:00Z',
        'status' => 'registered',
        'montonioOrderUuid' => 'mont-uuid-1',
        'merchantReference' => 'order-ref-001',
        'sender' => [
            'name' => 'Warehouse',
            'phoneCountryCode' => '+372',
            'phoneNumber' => '5551234',
            'streetAddress' => '1 Warehouse St',
            'locality' => 'Tallinn',
            'postalCode' => '10115',
            'country' => 'EE',
        ],
        'receiver' => [
            'name' => 'John Doe',
            'phoneCountryCode' => '+372',
            'phoneNumber' => '5559876',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'streetAddress' => '123 Main St',
            'locality' => 'Tartu',
            'postalCode' => '50001',
            'country' => 'EE',
        ],
        'parcels' => [
            ['weight' => 1.5, 'height' => 10.0, 'width' => 20.0, 'length' => 30.0],
        ],
        'shippingMethod' => [
            'type' => 'pickupPoint',
            'id' => 'pp-omniva-ee-123',
            'parcelHandoverMethod' => 'courierPickUp',
            'lockerSize' => 'M',
        ],
        'carrierShipmentId' => 'CARRIER-12345',
        'store' => [
            'id' => 'store-1',
            'name' => 'My Store',
        ],
        'products' => [
            [
                'sku' => 'SKU-001',
                'name' => 'Widget',
                'quantity' => 2,
                'price' => 9.99,
                'currency' => 'EUR',
            ],
        ],
    ];
}

test('fromArray maps all top-level fields', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->id)->toBe('ship-uuid-123')
        ->and($response->createdAt)->toBe('2025-01-15T10:00:00Z')
        ->and($response->montonioOrderUuid)->toBe('mont-uuid-1')
        ->and($response->merchantReference)->toBe('order-ref-001')
        ->and($response->carrierShipmentId)->toBe('CARRIER-12345');
});

test('maps ShipmentStatus enum', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->status)->toBe(ShipmentStatus::Registered);
});

test('maps sender response', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->sender)->toBeInstanceOf(ShipmentSenderResponse::class)
        ->and($response->sender->name)->toBe('Warehouse')
        ->and($response->sender->streetAddress)->toBe('1 Warehouse St')
        ->and($response->sender->country)->toBe('EE');
});

test('maps receiver response', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->receiver)->toBeInstanceOf(ShipmentReceiverResponse::class)
        ->and($response->receiver->name)->toBe('John Doe')
        ->and($response->receiver->firstName)->toBe('John')
        ->and($response->receiver->email)->toBe('john@example.com');
});

test('maps parcels', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->parcels)->toHaveCount(1)
        ->and($response->parcels[0])->toBeInstanceOf(ShipmentParcelResponse::class)
        ->and($response->parcels[0]->weight)->toBe(1.5)
        ->and($response->parcels[0]->height)->toBe(10.0);
});

test('maps shipping method response', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->shippingMethod)->toBeInstanceOf(ShipmentShippingMethodResponse::class)
        ->and($response->shippingMethod->type)->toBe('pickupPoint')
        ->and($response->shippingMethod->id)->toBe('pp-omniva-ee-123')
        ->and($response->shippingMethod->parcelHandoverMethod)->toBe('courierPickUp')
        ->and($response->shippingMethod->lockerSize)->toBe('M');
});

test('maps store and products', function () {
    $response = ShipmentResponse::fromArray(shipmentFixture());

    expect($response->store)->toBeInstanceOf(ShipmentStoreResponse::class)
        ->and($response->store->id)->toBe('store-1')
        ->and($response->store->name)->toBe('My Store')
        ->and($response->products)->toHaveCount(1)
        ->and($response->products[0])->toBeInstanceOf(ShipmentProductResponse::class)
        ->and($response->products[0]->sku)->toBe('SKU-001')
        ->and($response->products[0]->price)->toBe(9.99);
});

test('handles null products', function () {
    $data = shipmentFixture();
    unset($data['products']);

    $response = ShipmentResponse::fromArray($data);

    expect($response->products)->toBeNull();
});

test('handles null optional fields', function () {
    $data = shipmentFixture();
    unset($data['montonioOrderUuid'], $data['merchantReference'], $data['carrierShipmentId']);

    $response = ShipmentResponse::fromArray($data);

    expect($response->montonioOrderUuid)->toBeNull()
        ->and($response->merchantReference)->toBeNull()
        ->and($response->carrierShipmentId)->toBeNull();
});

test('fromArray with docs shipment-created fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/shipment-created.json');
    $response = ShipmentResponse::fromArray($data);

    expect($response->id)->toBe('1f83f4c1-cccc-4dd5-8eae-837e6a88362f')
        ->and($response->status)->toBe(ShipmentStatus::Pending)
        ->and($response->montonioOrderUuid)->toBe('240b8d02-1b59-4685-87e6-c37ff4a2bacc')
        ->and($response->merchantReference)->toBe('test 1')
        ->and($response->carrierShipmentId)->toBeNull()
        ->and($response->sender->name)->toBe('Sender Y')
        ->and($response->sender->phoneCountryCode)->toBe('372')
        ->and($response->sender->email)->toBe('support@montonio.com')
        ->and($response->receiver->name)->toBe('Receiver X')
        ->and($response->receiver->email)->toBe('support@montonio.com')
        ->and($response->parcels)->toHaveCount(1)
        ->and($response->parcels[0]->weight)->toBe(1.0)
        ->and($response->shippingMethod->type)->toBe('pickupPoint')
        ->and($response->shippingMethod->id)->toBe('377c3b06-0967-4ff2-b28a-372cab234898')
        ->and($response->store->id)->toBe('088ae409-ae24-4a3c-a640-5c269f732caa')
        ->and($response->products)->toHaveCount(2)
        ->and($response->products[0]->sku)->toBe('PROD-001')
        ->and($response->products[0]->name)->toBe('Wireless Headphones')
        ->and($response->products[0]->price)->toBe(79.99)
        ->and($response->products[1]->sku)->toBe('PROD-002')
        ->and($response->products[1]->quantity)->toBe(2);
});
