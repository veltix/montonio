<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Request\CreateLabelFileRequest;
use Veltix\Montonio\Shipping\Enum\OrderLabelsBy;
use Veltix\Montonio\Shipping\Enum\PageSize;

test('toArray includes shipmentIds', function () {
    $request = new CreateLabelFileRequest(shipmentIds: ['ship-1', 'ship-2']);

    $array = $request->toArray();
    expect($array['shipmentIds'])->toBe(['ship-1', 'ship-2'])
        ->and($array)->not->toHaveKey('pageSize');
});

test('toArray includes optional enums', function () {
    $request = new CreateLabelFileRequest(
        shipmentIds: ['ship-1'],
        pageSize: PageSize::A4,
        labelsPerPage: 4,
        orderLabelsBy: OrderLabelsBy::Carrier,
        synchronous: true,
    );

    $array = $request->toArray();
    expect($array['pageSize'])->toBe('A4')
        ->and($array['labelsPerPage'])->toBe(4)
        ->and($array['orderLabelsBy'])->toBe('carrier')
        ->and($array['synchronous'])->toBeTrue();
});

test('roundtrip toArray/fromArray', function () {
    $original = new CreateLabelFileRequest(
        shipmentIds: ['s-1', 's-2'],
        pageSize: PageSize::A6,
        orderLabelsBy: OrderLabelsBy::CreatedAt,
    );

    $restored = CreateLabelFileRequest::fromArray($original->toArray());

    expect($restored->shipmentIds)->toBe(['s-1', 's-2'])
        ->and($restored->pageSize)->toBe(PageSize::A6)
        ->and($restored->orderLabelsBy)->toBe(OrderLabelsBy::CreatedAt)
        ->and($restored->labelsPerPage)->toBeNull()
        ->and($restored->synchronous)->toBeNull();
});
