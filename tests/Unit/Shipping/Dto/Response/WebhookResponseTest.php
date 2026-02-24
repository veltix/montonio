<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\WebhookListResponse;
use Veltix\Montonio\Shipping\Dto\Response\WebhookResponse;

test('WebhookResponse maps all fields', function () {
    $response = WebhookResponse::fromArray([
        'id' => 'wh-1',
        'createdAt' => '2025-01-15T10:00:00Z',
        'url' => 'https://example.com/webhook',
        'enabledEvents' => ['shipment.registered', 'labelFile.ready'],
    ]);

    expect($response->id)->toBe('wh-1')
        ->and($response->createdAt)->toBe('2025-01-15T10:00:00Z')
        ->and($response->url)->toBe('https://example.com/webhook')
        ->and($response->enabledEvents)->toBe(['shipment.registered', 'labelFile.ready']);
});

test('WebhookListResponse maps data array', function () {
    $response = WebhookListResponse::fromArray([
        'data' => [
            [
                'id' => 'wh-1',
                'createdAt' => '2025-01-01T00:00:00Z',
                'url' => 'https://example.com/wh1',
                'enabledEvents' => ['shipment.registered'],
            ],
            [
                'id' => 'wh-2',
                'createdAt' => '2025-01-02T00:00:00Z',
                'url' => 'https://example.com/wh2',
                'enabledEvents' => ['labelFile.ready'],
            ],
        ],
    ]);

    expect($response->data)->toHaveCount(2)
        ->and($response->data[0])->toBeInstanceOf(WebhookResponse::class)
        ->and($response->data[0]->id)->toBe('wh-1')
        ->and($response->data[1]->id)->toBe('wh-2');
});

test('WebhookListResponse handles flat data', function () {
    $response = WebhookListResponse::fromArray([
        [
            'id' => 'wh-1',
            'createdAt' => '2025-01-01T00:00:00Z',
            'url' => 'https://example.com/wh',
            'enabledEvents' => [],
        ],
    ]);

    expect($response->data)->toHaveCount(1)
        ->and($response->data[0]->id)->toBe('wh-1');
});

test('docs fixture single webhook', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/webhook.json');
    $response = WebhookResponse::fromArray($data);

    expect($response->id)->toBe('3f922a3a-5063-405b-a489-a2a13a86a13b')
        ->and($response->createdAt)->toBe('2024-06-13T10:41:22.191Z')
        ->and($response->url)->toBe('https://webhook.site/305802f7-4bad-4401-b4ee-b4d89aeae6d2')
        ->and($response->enabledEvents)->toHaveCount(6)
        ->and($response->enabledEvents)->toContain('shipment.registered')
        ->and($response->enabledEvents)->toContain('labelFile.ready');
});

test('docs fixture webhook list', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/webhook-list.json');
    $response = WebhookListResponse::fromArray($data);

    expect($response->data)->toHaveCount(1)
        ->and($response->data[0]->id)->toBe('92965086-24a3-4fbd-919a-661142210c48')
        ->and($response->data[0]->url)->toBe('http://partner.montonio/shipmentEvents')
        ->and($response->data[0]->enabledEvents)->toBe(['shipment.registered']);
});
