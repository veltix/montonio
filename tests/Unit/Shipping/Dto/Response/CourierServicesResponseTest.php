<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\CourierService;
use Veltix\Montonio\Shipping\Dto\Response\CourierServicesResponse;

test('fromArray maps courierServices and countryCode', function () {
    $response = CourierServicesResponse::fromArray([
        'courierServices' => [
            [
                'id' => 'cs-1',
                'name' => 'Standard Courier',
                'type' => 'courier',
                'carrierCode' => 'dpd',
                'additionalServices' => [],
            ],
        ],
        'countryCode' => 'LT',
    ]);

    expect($response->courierServices)->toHaveCount(1)
        ->and($response->countryCode)->toBe('LT')
        ->and($response->courierServices[0])->toBeInstanceOf(CourierService::class);
});

test('CourierService maps all fields', function () {
    $service = CourierService::fromArray([
        'id' => 'cs-dpd-lt',
        'name' => 'DPD Standard',
        'type' => 'courier',
        'carrierCode' => 'dpd',
        'additionalServices' => [
            ['code' => 'cod'],
        ],
    ]);

    expect($service->id)->toBe('cs-dpd-lt')
        ->and($service->name)->toBe('DPD Standard')
        ->and($service->type)->toBe('courier')
        ->and($service->carrierCode)->toBe('dpd')
        ->and($service->additionalServices)->toHaveCount(1)
        ->and($service->additionalServices[0]->code)->toBe('cod');
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/courier-services.json');
    $response = CourierServicesResponse::fromArray($data);

    expect($response->countryCode)->toBe('EE')
        ->and($response->courierServices)->toHaveCount(1)
        ->and($response->courierServices[0]->id)->toBe('0ffb9b04-3927-462a-a393-4f1e21f2ee55')
        ->and($response->courierServices[0]->name)->toBe('Standard')
        ->and($response->courierServices[0]->type)->toBe('standard')
        ->and($response->courierServices[0]->carrierCode)->toBe('omniva')
        ->and($response->courierServices[0]->additionalServices)->toHaveCount(1)
        ->and($response->courierServices[0]->additionalServices[0]->code)->toBe('cod');
});
