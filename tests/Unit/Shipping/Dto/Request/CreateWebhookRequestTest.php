<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\CreateWebhookRequest;
use Veltix\Montonio\Shipping\Enum\ShippingWebhookEvent;

test('toArray maps url and events as strings', function () {
    $request = new CreateWebhookRequest(
        url: 'https://example.com/webhooks',
        enabledEvents: [
            ShippingWebhookEvent::ShipmentRegistered,
            ShippingWebhookEvent::LabelFileReady,
        ],
    );

    $array = $request->toArray();
    expect($array['url'])->toBe('https://example.com/webhooks')
        ->and($array['enabledEvents'])->toBe(['shipment.registered', 'labelFile.ready']);
});

test('fromArray creates enum array from strings', function () {
    $request = CreateWebhookRequest::fromArray([
        'url' => 'https://example.com/hook',
        'enabledEvents' => ['shipment.statusUpdated', 'labelFile.creationFailed'],
    ]);

    expect($request->url)->toBe('https://example.com/hook')
        ->and($request->enabledEvents)->toHaveCount(2)
        ->and($request->enabledEvents[0])->toBe(ShippingWebhookEvent::ShipmentStatusUpdated)
        ->and($request->enabledEvents[1])->toBe(ShippingWebhookEvent::LabelFileCreationFailed);
});

test('roundtrip toArray/fromArray', function () {
    $original = new CreateWebhookRequest(
        url: 'https://example.com/wh',
        enabledEvents: [
            ShippingWebhookEvent::ShipmentRegistered,
            ShippingWebhookEvent::ShipmentLabelsCreated,
            ShippingWebhookEvent::ShipmentStatusUpdated,
        ],
    );

    $restored = CreateWebhookRequest::fromArray($original->toArray());

    expect($restored->url)->toBe($original->url)
        ->and($restored->enabledEvents)->toHaveCount(3)
        ->and($restored->enabledEvents[0])->toBe(ShippingWebhookEvent::ShipmentRegistered)
        ->and($restored->enabledEvents[2])->toBe(ShippingWebhookEvent::ShipmentStatusUpdated);
});
