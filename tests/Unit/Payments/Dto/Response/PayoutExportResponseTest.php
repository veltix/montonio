<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Response\PayoutExportResponse;

test('fromArray maps url', function () {
    $response = PayoutExportResponse::fromArray([
        'url' => 'https://exports.montonio.com/payout-123.xlsx',
    ]);

    expect($response->url)->toBe('https://exports.montonio.com/payout-123.xlsx');
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/payout-export.json');
    $response = PayoutExportResponse::fromArray($data);

    expect($response->url)->toContain('s3.eu-central-1.amazonaws.com')
        ->and($response->url)->toContain('.xlsx');
});
