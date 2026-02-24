<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\PaymentLinkResponse;

test('fromArray maps uuid and url', function () {
    $response = PaymentLinkResponse::fromArray([
        'uuid' => 'pl-uuid-123',
        'url' => 'https://pay.montonio.com/link/pl-uuid-123',
    ]);

    expect($response->uuid)->toBe('pl-uuid-123')
        ->and($response->url)->toBe('https://pay.montonio.com/link/pl-uuid-123');
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/payment-link.json');
    $response = PaymentLinkResponse::fromArray($data);

    expect($response->uuid)->toBe('1088b447-a9ab-42aa-b473-ea6ba174c671')
        ->and($response->url)->toBe('https://pay.montonio.com/1088b447-a9ab-42aa-b473-ea6ba174c671');
});
