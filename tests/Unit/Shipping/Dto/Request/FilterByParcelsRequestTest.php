<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\FilterByParcelsRequest;
use Veltix\Montonio\Shipping\Dto\Request\FilterParcel;

test('toArray maps parcels array', function () {
    $request = new FilterByParcelsRequest(
        parcels: [
            new FilterParcel(weight: 1.0),
            new FilterParcel(weight: 2.5, height: 10.0, width: 20.0, length: 30.0),
        ],
    );

    $array = $request->toArray();
    expect($array['parcels'])->toHaveCount(2)
        ->and($array['parcels'][0])->toBe(['weight' => 1.0])
        ->and($array['parcels'][1])->toBe(['weight' => 2.5, 'height' => 10.0, 'width' => 20.0, 'length' => 30.0]);
});

test('FilterParcel toArray filters null dimensions', function () {
    $parcel = new FilterParcel(weight: 0.5, height: 5.0);

    $array = $parcel->toArray();
    expect($array)->toBe(['weight' => 0.5, 'height' => 5.0])
        ->and($array)->not->toHaveKey('width')
        ->and($array)->not->toHaveKey('length');
});

test('roundtrip toArray/fromArray', function () {
    $original = new FilterByParcelsRequest(
        parcels: [new FilterParcel(weight: 3.0, height: 15.0, width: 25.0, length: 35.0)],
    );

    $restored = FilterByParcelsRequest::fromArray($original->toArray());

    expect($restored->parcels)->toHaveCount(1)
        ->and($restored->parcels[0]->weight)->toBe(3.0)
        ->and($restored->parcels[0]->height)->toBe(15.0)
        ->and($restored->parcels[0]->width)->toBe(25.0)
        ->and($restored->parcels[0]->length)->toBe(35.0);
});
