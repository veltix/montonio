<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\RefundResponse;
use Veltix\Montonio\Payments\Enum\RefundStatus;
use Veltix\Montonio\Payments\Enum\RefundType;

test('fromArray maps all fields', function () {
    $response = RefundResponse::fromArray([
        'uuid' => 'refund-uuid-1',
        'amount' => '25.50',
        'status' => 'SUCCESSFUL',
        'currency' => 'EUR',
        'createdAt' => '2025-01-15T10:00:00Z',
        'type' => 'PARTIAL_REFUND',
    ]);

    expect($response->uuid)->toBe('refund-uuid-1')
        ->and($response->amount)->toBe(25.50)
        ->and($response->currency)->toBe('EUR')
        ->and($response->createdAt)->toBe('2025-01-15T10:00:00Z');
});

test('maps RefundStatus enum', function () {
    $response = RefundResponse::fromArray([
        'uuid' => 'r-1',
        'amount' => '10.00',
        'status' => 'PENDING',
        'currency' => 'EUR',
        'createdAt' => '2025-01-01T00:00:00Z',
        'type' => 'FULL_REFUND',
    ]);

    expect($response->status)->toBe(RefundStatus::PENDING);
});

test('maps RefundType enum', function () {
    $response = RefundResponse::fromArray([
        'uuid' => 'r-2',
        'amount' => '100.00',
        'status' => 'PROCESSING',
        'currency' => 'PLN',
        'createdAt' => '2025-02-01T00:00:00Z',
        'type' => 'FULL_REFUND',
    ]);

    expect($response->type)->toBe(RefundType::FULL_REFUND);
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/refund.json');
    $response = RefundResponse::fromArray($data);

    expect($response->uuid)->toBe('97b20084-319a-4cce-92f5-56d3b41a986a')
        ->and($response->amount)->toBe(25.0)
        ->and($response->status)->toBe(RefundStatus::PENDING)
        ->and($response->currency)->toBe('EUR')
        ->and($response->createdAt)->toBe('2023-05-23T08:37:55.534Z')
        ->and($response->type)->toBe(RefundType::PARTIAL_REFUND);
});
