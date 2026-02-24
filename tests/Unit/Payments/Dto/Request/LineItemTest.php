<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\LineItem;

test('toArray returns all fields', function () {
    $item = new LineItem(name: 'Widget', quantity: 3, finalPrice: 29.99);

    expect($item->toArray())->toBe([
        'name' => 'Widget',
        'quantity' => 3,
        'finalPrice' => 29.99,
    ]);
});

test('fromArray creates LineItem', function () {
    $item = LineItem::fromArray(['name' => 'Gadget', 'quantity' => 1, 'finalPrice' => '15.50']);

    expect($item->name)->toBe('Gadget')
        ->and($item->quantity)->toBe(1)
        ->and($item->finalPrice)->toBe(15.50);
});

test('roundtrip toArray/fromArray', function () {
    $original = new LineItem(name: 'Product', quantity: 5, finalPrice: 100.00);
    $restored = LineItem::fromArray($original->toArray());

    expect($restored->name)->toBe($original->name)
        ->and($restored->quantity)->toBe($original->quantity)
        ->and($restored->finalPrice)->toBe($original->finalPrice);
});
