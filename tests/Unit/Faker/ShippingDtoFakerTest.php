<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\CreateLabelFileRequest;
use Veltix\Montonio\Shipping\Dto\Request\CreateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Request\CreateWebhookRequest;
use Veltix\Montonio\Shipping\Dto\Request\FilterByParcelsRequest;
use Veltix\Montonio\Shipping\Dto\Request\FilterParcel;
use Veltix\Montonio\Shipping\Dto\Request\RatesItem;
use Veltix\Montonio\Shipping\Dto\Request\RatesParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentProduct;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentReceiver;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentSender;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentShippingMethod;
use Veltix\Montonio\Shipping\Dto\Request\ShippingRatesRequest;
use Veltix\Montonio\Shipping\Dto\Request\UpdateShipmentRequest;
use Veltix\Montonio\Shipping\Enum\DimensionUnit;
use Veltix\Montonio\Shipping\Enum\OrderLabelsBy;
use Veltix\Montonio\Shipping\Enum\PageSize;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;
use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;
use Veltix\Montonio\Shipping\Enum\WeightUnit;
use Veltix\Montonio\Webhook\Dto\ShippingWebhookPayload;

use function Pest\Faker\fake;

test('ShipmentSender with random data roundtrips', function () {
    $sender = new ShipmentSender(
        name: fake()->name(),
        phoneCountryCode: fake()->numerify('+###'),
        phoneNumber: fake()->numerify('#######'),
        streetAddress: fake()->streetAddress(),
        locality: fake()->city(),
        postalCode: fake()->postcode(),
        country: fake()->countryCode(),
        region: fake()->word(),
        email: fake()->safeEmail(),
        companyName: fake()->company(),
    );

    $array = $sender->toArray();
    $restored = ShipmentSender::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('ShipmentReceiver with random data roundtrips', function () {
    $receiver = new ShipmentReceiver(
        name: fake()->name(),
        phoneCountryCode: fake()->numerify('+###'),
        phoneNumber: fake()->numerify('#######'),
        firstName: fake()->firstName(),
        lastName: fake()->lastName(),
        streetAddress: fake()->streetAddress(),
        locality: fake()->city(),
        postalCode: fake()->postcode(),
        country: fake()->countryCode(),
        region: fake()->word(),
        email: fake()->safeEmail(),
        companyName: fake()->company(),
    );

    $array = $receiver->toArray();
    $restored = ShipmentReceiver::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('ShipmentParcel with random data roundtrips', function () {
    $parcel = new ShipmentParcel(
        weight: fake()->randomFloat(2, 0.1, 50),
        height: fake()->randomFloat(2, 1, 100),
        width: fake()->randomFloat(2, 1, 100),
        length: fake()->randomFloat(2, 1, 100),
    );

    $array = $parcel->toArray();
    $restored = ShipmentParcel::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('ShipmentProduct with random data roundtrips', function () {
    $product = new ShipmentProduct(
        sku: fake()->bothify('SKU-####-??'),
        name: fake()->word(),
        quantity: fake()->numberBetween(1, 100),
        barcode: fake()->ean13(),
        price: fake()->randomFloat(2, 1, 999),
        currency: fake()->currencyCode(),
        attributes: ['color' => fake()->colorName()],
        imageUrl: fake()->url(),
        storeProductUrl: fake()->url(),
        description: fake()->sentence(),
    );

    $array = $product->toArray();
    $restored = ShipmentProduct::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('ShipmentShippingMethod with random data roundtrips', function () {
    $method = new ShipmentShippingMethod(
        type: fake()->randomElement(ShippingMethodType::cases()),
        id: fake()->uuid(),
    );

    $array = $method->toArray();
    $restored = ShipmentShippingMethod::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CreateShipmentRequest with random data roundtrips', function () {
    $request = new CreateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(
            type: fake()->randomElement(ShippingMethodType::cases()),
            id: fake()->uuid(),
        ),
        receiver: new ShipmentReceiver(
            name: fake()->name(),
            phoneCountryCode: fake()->numerify('+###'),
            phoneNumber: fake()->numerify('#######'),
            email: fake()->safeEmail(),
            streetAddress: fake()->streetAddress(),
            locality: fake()->city(),
            country: fake()->countryCode(),
        ),
        parcels: [
            new ShipmentParcel(
                weight: fake()->randomFloat(2, 0.1, 50),
                height: fake()->randomFloat(2, 1, 100),
                width: fake()->randomFloat(2, 1, 100),
                length: fake()->randomFloat(2, 1, 100),
            ),
        ],
        sender: new ShipmentSender(
            name: fake()->name(),
            phoneCountryCode: fake()->numerify('+###'),
            phoneNumber: fake()->numerify('#######'),
        ),
        merchantReference: fake()->uuid(),
        montonioOrderUuid: fake()->uuid(),
        orderComment: fake()->sentence(),
        products: [
            new ShipmentProduct(
                sku: fake()->bothify('SKU-####'),
                name: fake()->word(),
                quantity: fake()->numberBetween(1, 10),
            ),
        ],
        synchronous: fake()->boolean(),
    );

    $array = $request->toArray();
    $restored = CreateShipmentRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('UpdateShipmentRequest with random data roundtrips', function () {
    $request = new UpdateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(
            type: fake()->randomElement(ShippingMethodType::cases()),
            id: fake()->uuid(),
        ),
        receiver: new ShipmentReceiver(
            name: fake()->name(),
            phoneCountryCode: fake()->numerify('+###'),
            phoneNumber: fake()->numerify('#######'),
        ),
        sender: new ShipmentSender(
            name: fake()->name(),
            phoneCountryCode: fake()->numerify('+###'),
            phoneNumber: fake()->numerify('#######'),
        ),
        parcels: [
            new ShipmentParcel(
                weight: fake()->randomFloat(2, 0.1, 50),
            ),
        ],
    );

    $array = $request->toArray();
    $restored = UpdateShipmentRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('FilterParcel with random data roundtrips', function () {
    $parcel = new FilterParcel(
        weight: fake()->randomFloat(2, 0.1, 50),
        height: fake()->randomFloat(2, 1, 100),
        width: fake()->randomFloat(2, 1, 100),
        length: fake()->randomFloat(2, 1, 100),
    );

    $array = $parcel->toArray();
    $restored = FilterParcel::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('FilterByParcelsRequest with random data roundtrips', function () {
    $request = new FilterByParcelsRequest(
        parcels: [
            new FilterParcel(weight: fake()->randomFloat(2, 0.1, 50)),
            new FilterParcel(
                weight: fake()->randomFloat(2, 0.1, 50),
                height: fake()->randomFloat(2, 1, 100),
                width: fake()->randomFloat(2, 1, 100),
                length: fake()->randomFloat(2, 1, 100),
            ),
        ],
    );

    $array = $request->toArray();
    $restored = FilterByParcelsRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('RatesItem with random data roundtrips', function () {
    $item = new RatesItem(
        length: fake()->randomFloat(2, 1, 100),
        width: fake()->randomFloat(2, 1, 100),
        height: fake()->randomFloat(2, 1, 100),
        weight: fake()->randomFloat(2, 0.1, 50),
        dimensionUnit: fake()->randomElement(DimensionUnit::cases()),
        weightUnit: fake()->randomElement(WeightUnit::cases()),
        quantity: fake()->numberBetween(1, 100),
    );

    $array = $item->toArray();
    $restored = RatesItem::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('ShippingRatesRequest with random data roundtrips', function () {
    $request = new ShippingRatesRequest(
        destination: fake()->countryCode(),
        parcels: [
            new RatesParcel(
                items: [
                    new RatesItem(
                        length: fake()->randomFloat(2, 1, 100),
                        width: fake()->randomFloat(2, 1, 100),
                        height: fake()->randomFloat(2, 1, 100),
                        weight: fake()->randomFloat(2, 0.1, 50),
                    ),
                ],
            ),
        ],
    );

    $array = $request->toArray();
    $restored = ShippingRatesRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CreateLabelFileRequest with random data roundtrips', function () {
    $request = new CreateLabelFileRequest(
        shipmentIds: [fake()->uuid(), fake()->uuid()],
        pageSize: fake()->randomElement(PageSize::cases()),
        labelsPerPage: fake()->numberBetween(1, 4),
        orderLabelsBy: fake()->randomElement(OrderLabelsBy::cases()),
        synchronous: fake()->boolean(),
    );

    $array = $request->toArray();
    $restored = CreateLabelFileRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CreateWebhookRequest with random data roundtrips', function () {
    $events = fake()->randomElements(
        ShippingWebhookEvent::cases(),
        fake()->numberBetween(1, count(ShippingWebhookEvent::cases())),
    );

    $request = new CreateWebhookRequest(
        url: fake()->url(),
        enabledEvents: $events,
    );

    $array = $request->toArray();
    $restored = CreateWebhookRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('ShippingWebhookPayload with random data', function () {
    $data = (object) [
        'eventId' => fake()->uuid(),
        'shipmentId' => fake()->uuid(),
        'created' => fake()->iso8601(),
        'data' => (object) ['status' => fake()->word()],
        'eventType' => fake()->randomElement(ShippingWebhookEvent::cases())->value,
        'iat' => fake()->unixTime(),
        'exp' => fake()->unixTime(),
    ];

    $payload = ShippingWebhookPayload::fromObject($data);

    expect($payload->eventId)->toBe($data->eventId)
        ->and($payload->shipmentId)->toBe($data->shipmentId)
        ->and($payload->created)->toBe($data->created)
        ->and($payload->eventType)->toBe(ShippingWebhookEvent::from($data->eventType))
        ->and($payload->iat)->toBe((int) $data->iat)
        ->and($payload->exp)->toBe((int) $data->exp);
});
