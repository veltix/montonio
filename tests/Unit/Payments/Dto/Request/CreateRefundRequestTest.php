<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\CreateRefundRequest;

test('toArray returns all fields', function () {
    $request = new CreateRefundRequest(
        orderUuid: 'order-uuid-123',
        amount: 25.50,
        idempotencyKey: 'idem-key-456',
    );

    expect($request->toArray())->toBe([
        'orderUuid' => 'order-uuid-123',
        'amount' => 25.50,
        'idempotencyKey' => 'idem-key-456',
    ]);
});

test('fromArray creates request', function () {
    $request = CreateRefundRequest::fromArray([
        'orderUuid' => 'uuid-1',
        'amount' => '10.00',
        'idempotencyKey' => 'key-1',
    ]);

    expect($request->orderUuid)->toBe('uuid-1')
        ->and($request->amount)->toBe(10.00)
        ->and($request->idempotencyKey)->toBe('key-1');
});

test('roundtrip toArray/fromArray', function () {
    $original = new CreateRefundRequest(orderUuid: 'uuid', amount: 5.99, idempotencyKey: 'key');
    $restored = CreateRefundRequest::fromArray($original->toArray());

    expect($restored->orderUuid)->toBe($original->orderUuid)
        ->and($restored->amount)->toBe($original->amount)
        ->and($restored->idempotencyKey)->toBe($original->idempotencyKey);
});
