<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\PickupPoint;
use Veltix\Montonio\Shipping\Dto\Response\PickupPointAdditionalService;
use Veltix\Montonio\Shipping\Dto\Response\PickupPointsResponse;

test('fromArray maps pickupPoints and countryCode', function () {
    $response = PickupPointsResponse::fromArray([
        'pickupPoints' => [
            [
                'id' => 'pp-1',
                'name' => 'Omniva Tallinn',
                'type' => 'parcelMachine',
                'streetAddress' => '1 Main St',
                'locality' => 'Tallinn',
                'postalCode' => '10115',
                'carrierCode' => 'omniva',
                'additionalServices' => [],
            ],
        ],
        'countryCode' => 'EE',
    ]);

    expect($response->pickupPoints)->toHaveCount(1)
        ->and($response->countryCode)->toBe('EE')
        ->and($response->pickupPoints[0])->toBeInstanceOf(PickupPoint::class);
});

test('PickupPoint maps all fields', function () {
    $point = PickupPoint::fromArray([
        'id' => 'pp-omniva-1',
        'name' => 'Kristiine Keskus',
        'type' => 'parcelMachine',
        'streetAddress' => 'Endla 45',
        'locality' => 'Tallinn',
        'postalCode' => '10615',
        'carrierCode' => 'omniva',
        'additionalServices' => [
            ['code' => 'cod'],
        ],
    ]);

    expect($point->id)->toBe('pp-omniva-1')
        ->and($point->name)->toBe('Kristiine Keskus')
        ->and($point->type)->toBe('parcelMachine')
        ->and($point->streetAddress)->toBe('Endla 45')
        ->and($point->locality)->toBe('Tallinn')
        ->and($point->postalCode)->toBe('10615')
        ->and($point->carrierCode)->toBe('omniva');
});

test('PickupPoint maps additionalServices', function () {
    $point = PickupPoint::fromArray([
        'id' => 'pp-1',
        'name' => 'Test',
        'type' => 'postOffice',
        'streetAddress' => 'St',
        'locality' => 'City',
        'postalCode' => '00000',
        'carrierCode' => 'dpd',
        'additionalServices' => [
            ['code' => 'cod'],
            ['code' => 'ageVerification'],
        ],
    ]);

    expect($point->additionalServices)->toHaveCount(2)
        ->and($point->additionalServices[0])->toBeInstanceOf(PickupPointAdditionalService::class)
        ->and($point->additionalServices[0]->code)->toBe('cod')
        ->and($point->additionalServices[1]->code)->toBe('ageVerification');
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/pickup-points.json');
    $response = PickupPointsResponse::fromArray($data);

    expect($response->countryCode)->toBe('EE')
        ->and($response->pickupPoints)->toHaveCount(2);

    $first = $response->pickupPoints[0];
    expect($first->id)->toBe('98b391d7-5299-447c-9ad7-6b4042ef8b2f')
        ->and($first->name)->toBe('Laagri Coop Maksimarketi pakiautomaat')
        ->and($first->type)->toBe('parcelMachine')
        ->and($first->carrierCode)->toBe('omniva')
        ->and($first->additionalServices)->toHaveCount(2)
        ->and($first->additionalServices[0]->code)->toBe('cod')
        ->and($first->additionalServices[1]->code)->toBe('ageVerification');

    $second = $response->pickupPoints[1];
    expect($second->additionalServices)->toBe([]);
});
