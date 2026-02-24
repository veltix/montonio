<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\LabelFileResponse;
use Veltix\Montonio\Shipping\Enum\LabelFileStatus;

test('pending label file has null url', function () {
    $response = LabelFileResponse::fromArray([
        'id' => 'lf-1',
        'status' => 'pending',
    ]);

    expect($response->id)->toBe('lf-1')
        ->and($response->status)->toBe(LabelFileStatus::Pending)
        ->and($response->labelFileUrl)->toBeNull()
        ->and($response->pageSize)->toBeNull();
});

test('ready label file has url', function () {
    $response = LabelFileResponse::fromArray([
        'id' => 'lf-2',
        'status' => 'ready',
        'pageSize' => 'A4',
        'labelsPerPage' => 4,
        'orderLabelsBy' => 'carrier',
        'labelFileUrl' => 'https://labels.montonio.com/lf-2.pdf',
    ]);

    expect($response->id)->toBe('lf-2')
        ->and($response->status)->toBe(LabelFileStatus::Ready)
        ->and($response->pageSize)->toBe('A4')
        ->and($response->labelsPerPage)->toBe(4)
        ->and($response->orderLabelsBy)->toBe('carrier')
        ->and($response->labelFileUrl)->toBe('https://labels.montonio.com/lf-2.pdf');
});

test('docs fixture pending label file', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/label-file-pending.json');
    $response = LabelFileResponse::fromArray($data);

    expect($response->id)->toBe('d58f2e2f-7460-4916-8463-8644f917b22b')
        ->and($response->status)->toBe(LabelFileStatus::Pending)
        ->and($response->pageSize)->toBe('A4')
        ->and($response->labelsPerPage)->toBe(4)
        ->and($response->orderLabelsBy)->toBe('createdAt')
        ->and($response->labelFileUrl)->toBeNull();
});

test('docs fixture ready label file', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/label-file-ready.json');
    $response = LabelFileResponse::fromArray($data);

    expect($response->id)->toBe('d58f2e2f-7460-4916-8463-8644f917b22b')
        ->and($response->status)->toBe(LabelFileStatus::Ready)
        ->and($response->labelFileUrl)->toContain('shippingv2-labels');
});
