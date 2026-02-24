<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\SessionResponse;

test('fromArray maps uuid', function () {
    $response = SessionResponse::fromArray(['uuid' => 'session-uuid-123']);

    expect($response->uuid)->toBe('session-uuid-123');
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/session.json');
    $response = SessionResponse::fromArray($data);

    expect($response->uuid)->toBe('087a9fb5-7a85-4e1e-b3f7-2546faab9a97');
});
