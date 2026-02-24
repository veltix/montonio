<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;
use Veltix\Montonio\Webhook\Dto\ShippingWebhookPayload;

test('fromObject maps all fields', function () {
    $data = (object) [
        'eventId' => 'evt-1',
        'shipmentId' => 'ship-1',
        'created' => '2025-01-15T10:00:00Z',
        'data' => (object) ['status' => 'registered', 'trackingUrl' => 'https://track.example.com'],
        'eventType' => 'shipment.registered',
        'iat' => 1700000000,
        'exp' => 1700003600,
    ];

    $payload = ShippingWebhookPayload::fromObject($data);

    expect($payload->eventId)->toBe('evt-1')
        ->and($payload->shipmentId)->toBe('ship-1')
        ->and($payload->created)->toBe('2025-01-15T10:00:00Z')
        ->and($payload->data)->toBe(['status' => 'registered', 'trackingUrl' => 'https://track.example.com'])
        ->and($payload->data)->toBeArray()
        ->and($payload->iat)->toBe(1700000000)
        ->and($payload->exp)->toBe(1700003600);
});

test('maps ShippingWebhookEvent enum', function () {
    $data = (object) [
        'eventId' => 'evt-2',
        'shipmentId' => 'ship-2',
        'created' => '2025-01-16T10:00:00Z',
        'data' => (object) [],
        'eventType' => 'labelFile.ready',
        'iat' => 1,
        'exp' => 2,
    ];

    $payload = ShippingWebhookPayload::fromObject($data);

    expect($payload->eventType)->toBe(ShippingWebhookEvent::LabelFileReady);
});

test('handles nullable shipmentId', function () {
    $data = (object) [
        'eventId' => 'evt-3',
        'created' => '2025-01-17T10:00:00Z',
        'data' => (object) [],
        'eventType' => 'labelFile.creationFailed',
        'iat' => 1,
        'exp' => 2,
    ];

    $payload = ShippingWebhookPayload::fromObject($data);

    expect($payload->shipmentId)->toBeNull();
});

test('fromObject with docs fixture', function () {
    $fixture = \Veltix\Montonio\Tests\fixture('Shipping/webhook-payload.json');
    $data = json_decode(json_encode($fixture));

    $payload = ShippingWebhookPayload::fromObject($data);

    expect($payload->eventId)->toBe('e1be81fb-2355-44b2-9f28-2b1a691151bb')
        ->and($payload->shipmentId)->toBe('87f55147-7765-4eb6-9bb1-3c1a4b05a435')
        ->and($payload->created)->toBe('2024-06-13T10:51:57.322Z')
        ->and($payload->eventType)->toBe(ShippingWebhookEvent::ShipmentRegistered)
        ->and($payload->data)->toBeArray()
        ->and($payload->data['status'])->toBe('registered')
        ->and($payload->iat)->toBe(1718275917)
        ->and($payload->exp)->toBe(1718880717);
});
