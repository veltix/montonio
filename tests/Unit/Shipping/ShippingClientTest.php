<?php

declare(strict_types=1);

use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Http\HttpClient;
use Veltix\Montonio\Http\ShippingHttpClient;
use Veltix\Montonio\Shipping\Dto\Request\CreateLabelFileRequest;
use Veltix\Montonio\Shipping\Dto\Request\CreateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Request\CreateWebhookRequest;
use Veltix\Montonio\Shipping\Dto\Request\FilterByParcelsRequest;
use Veltix\Montonio\Shipping\Dto\Request\FilterParcel;
use Veltix\Montonio\Shipping\Dto\Request\RatesItem;
use Veltix\Montonio\Shipping\Dto\Request\RatesParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentParcel;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentReceiver;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentShippingMethod;
use Veltix\Montonio\Shipping\Dto\Request\ShippingRatesRequest;
use Veltix\Montonio\Shipping\Dto\Request\UpdateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Response\CarriersResponse;
use Veltix\Montonio\Shipping\Dto\Response\CourierServicesResponse;
use Veltix\Montonio\Shipping\Dto\Response\LabelFileResponse;
use Veltix\Montonio\Shipping\Dto\Response\PickupPointsResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShippingMethodsResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShippingRatesResponse;
use Veltix\Montonio\Shipping\Dto\Response\WebhookListResponse;
use Veltix\Montonio\Shipping\Dto\Response\WebhookResponse;
use Veltix\Montonio\Shipping\Enum\PickupPointSubtype;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;
use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;
use Veltix\Montonio\Shipping\ShippingClient;

use function Veltix\Montonio\Tests\fixture;
use function Veltix\Montonio\Tests\jsonResponse;
use function Veltix\Montonio\Tests\mockPsrClient;
use function Veltix\Montonio\Tests\rawResponse;
use function Veltix\Montonio\Tests\testConfig;

function shippingClientWithResponse($response): array
{
    $mock = mockPsrClient($response);
    $config = testConfig($mock->client);
    $httpClient = new HttpClient($config);
    $jwtFactory = new JwtFactory($config);
    $shippingHttp = new ShippingHttpClient($httpClient, $jwtFactory, $config);
    $client = new ShippingClient($shippingHttp);

    return [$client, $mock];
}

function minShipmentFixture(): array
{
    return [
        'id' => 'ship-1',
        'createdAt' => '2025-01-01T00:00:00Z',
        'status' => 'pending',
        'sender' => ['name' => 'Sender', 'phoneCountryCode' => '+372', 'phoneNumber' => '123'],
        'receiver' => ['name' => 'Receiver', 'phoneCountryCode' => '+372', 'phoneNumber' => '456'],
        'parcels' => [['weight' => 1.0]],
        'shippingMethod' => ['type' => 'courier', 'id' => 'c-1'],
        'store' => ['id' => 'store-1', 'name' => 'Store'],
    ];
}

test('getCarriers returns CarriersResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/carriers.json')));

    $result = $client->getCarriers();

    expect($result)->toBeInstanceOf(CarriersResponse::class)
        ->and($result->carriers)->toHaveCount(4)
        ->and($result->carriers[0]->code)->toBe('smartpost')
        ->and($result->carriers[1]->code)->toBe('omniva')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/carriers');
});

test('getShippingMethods returns ShippingMethodsResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipping-methods.json')));

    $result = $client->getShippingMethods();

    expect($result)->toBeInstanceOf(ShippingMethodsResponse::class)
        ->and($result->countries)->toHaveCount(1)
        ->and($result->countries[0]->countryCode)->toBe('EE')
        ->and($result->countries[0]->carriers)->toHaveCount(2)
        ->and((string) $mock->lastRequest()->getUri())->toContain('/shipping-methods');
});

test('getPickupPoints without type', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/pickup-points.json')));

    $result = $client->getPickupPoints('omniva', 'EE');

    expect($result)->toBeInstanceOf(PickupPointsResponse::class)
        ->and($result->pickupPoints)->toHaveCount(2)
        ->and($result->countryCode)->toBe('EE')
        ->and($result->pickupPoints[0]->name)->toBe('Laagri Coop Maksimarketi pakiautomaat');

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('carrierCode=omniva')
        ->and($url)->toContain('countryCode=EE')
        ->and($url)->not->toContain('type=');
});

test('getPickupPoints with type', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/pickup-points.json')));

    $client->getPickupPoints('dpd', 'LT', PickupPointSubtype::ParcelMachine);

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('type=parcelMachine');
});

test('getCourierServices returns CourierServicesResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/courier-services.json')));

    $result = $client->getCourierServices('omniva', 'EE');

    expect($result)->toBeInstanceOf(CourierServicesResponse::class)
        ->and($result->courierServices)->toHaveCount(1)
        ->and($result->courierServices[0]->name)->toBe('Standard');

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('carrierCode=omniva')
        ->and($url)->toContain('countryCode=EE');
});

test('filterShippingMethodsByParcels without source', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipping-methods.json')));

    $request = new FilterByParcelsRequest(parcels: [new FilterParcel(weight: 1.0)]);
    $result = $client->filterShippingMethodsByParcels('EE', $request);

    expect($result)->toBeInstanceOf(ShippingMethodsResponse::class);

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('destination=EE')
        ->and($url)->not->toContain('source=');
});

test('filterShippingMethodsByParcels with source', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipping-methods.json')));

    $request = new FilterByParcelsRequest(parcels: [new FilterParcel(weight: 2.0)]);
    $client->filterShippingMethodsByParcels('LT', $request, 'EE');

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('destination=LT')
        ->and($url)->toContain('source=EE');
});

test('getShippingRates without optional params', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipping-rates.json')));

    $request = new ShippingRatesRequest(
        destination: 'EE',
        parcels: [new RatesParcel(items: [new RatesItem(length: 10, width: 10, height: 10, weight: 1)])],
    );
    $result = $client->getShippingRates($request);

    expect($result)->toBeInstanceOf(ShippingRatesResponse::class);

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->not->toContain('carrierCode=')
        ->and($url)->not->toContain('shippingMethodType=');
});

test('getShippingRates with optional params', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipping-rates.json')));

    $request = new ShippingRatesRequest(
        destination: 'EE',
        parcels: [new RatesParcel(items: [new RatesItem(length: 10, width: 10, height: 10, weight: 1)])],
    );
    $client->getShippingRates($request, 'omniva', ShippingMethodType::PickupPoint);

    $url = (string) $mock->lastRequest()->getUri();
    expect($url)->toContain('carrierCode=omniva')
        ->and($url)->toContain('shippingMethodType=pickupPoint');
});

test('createShipment returns ShipmentResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipment-created.json')));

    $request = new CreateShipmentRequest(
        shippingMethod: new ShipmentShippingMethod(type: ShippingMethodType::Courier, id: 'c-1'),
        receiver: new ShipmentReceiver(name: 'Receiver', phoneCountryCode: '+372', phoneNumber: '456'),
        parcels: [new ShipmentParcel(weight: 1.0)],
    );
    $result = $client->createShipment($request);

    expect($result)->toBeInstanceOf(ShipmentResponse::class)
        ->and($result->id)->toBe('1f83f4c1-cccc-4dd5-8eae-837e6a88362f')
        ->and($result->products)->toHaveCount(2)
        ->and($mock->lastRequest()->getMethod())->toBe('POST')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/shipments');
});

test('updateShipment returns ShipmentResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipment-created.json')));

    $request = new UpdateShipmentRequest(
        receiver: new ShipmentReceiver(name: 'Updated', phoneCountryCode: '+372', phoneNumber: '789'),
    );
    $result = $client->updateShipment('ship-1', $request);

    expect($result)->toBeInstanceOf(ShipmentResponse::class)
        ->and($mock->lastRequest()->getMethod())->toBe('PATCH')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/shipments/ship-1');
});

test('getShipment returns ShipmentResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/shipment-created.json')));

    $result = $client->getShipment('1f83f4c1-cccc-4dd5-8eae-837e6a88362f');

    expect($result)->toBeInstanceOf(ShipmentResponse::class)
        ->and($result->id)->toBe('1f83f4c1-cccc-4dd5-8eae-837e6a88362f')
        ->and($result->sender->name)->toBe('Sender Y')
        ->and($result->receiver->name)->toBe('Receiver X')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/shipments/1f83f4c1-cccc-4dd5-8eae-837e6a88362f');
});

test('createLabelFile returns LabelFileResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/label-file-pending.json')));

    $request = new CreateLabelFileRequest(shipmentIds: ['ship-1', 'ship-2']);
    $result = $client->createLabelFile($request);

    expect($result)->toBeInstanceOf(LabelFileResponse::class)
        ->and($result->id)->toBe('d58f2e2f-7460-4916-8463-8644f917b22b')
        ->and($result->status->value)->toBe('pending')
        ->and($result->labelFileUrl)->toBeNull()
        ->and((string) $mock->lastRequest()->getUri())->toContain('/label-files');
});

test('getLabelFile returns LabelFileResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/label-file-ready.json')));

    $result = $client->getLabelFile('d58f2e2f-7460-4916-8463-8644f917b22b');

    expect($result)->toBeInstanceOf(LabelFileResponse::class)
        ->and($result->status->value)->toBe('ready')
        ->and($result->labelFileUrl)->toContain('shippingv2-labels')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/label-files/d58f2e2f-7460-4916-8463-8644f917b22b');
});

test('createWebhook returns WebhookResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/webhook.json')));

    $request = new CreateWebhookRequest(
        url: 'https://webhook.site/305802f7-4bad-4401-b4ee-b4d89aeae6d2',
        enabledEvents: [ShippingWebhookEvent::ShipmentRegistered],
    );
    $result = $client->createWebhook($request);

    expect($result)->toBeInstanceOf(WebhookResponse::class)
        ->and($result->id)->toBe('3f922a3a-5063-405b-a489-a2a13a86a13b')
        ->and($result->enabledEvents)->toHaveCount(6)
        ->and((string) $mock->lastRequest()->getUri())->toContain('/webhooks');
});

test('listWebhooks returns WebhookListResponse', function () {
    [$client, $mock] = shippingClientWithResponse(jsonResponse(200, fixture('Shipping/webhook-list.json')));

    $result = $client->listWebhooks();

    expect($result)->toBeInstanceOf(WebhookListResponse::class)
        ->and($result->data)->toHaveCount(1)
        ->and($result->data[0]->id)->toBe('92965086-24a3-4fbd-919a-661142210c48')
        ->and($result->data[0]->url)->toBe('http://partner.montonio/shipmentEvents')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/webhooks');
});

test('deleteWebhook returns void', function () {
    [$client, $mock] = shippingClientWithResponse(rawResponse(204, ''));

    $result = $client->deleteWebhook('wh-1');

    expect($result)->toBeNull()
        ->and($mock->lastRequest()->getMethod())->toBe('DELETE')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/webhooks/wh-1');
});
