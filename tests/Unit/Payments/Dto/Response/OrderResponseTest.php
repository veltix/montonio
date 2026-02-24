<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\Address;
use Veltix\Montonio\Payments\Dto\Request\LineItem;
use Veltix\Montonio\Payments\Dto\Response\OrderResponse;
use Veltix\Montonio\Payments\Dto\Response\PaymentIntent;
use Veltix\Montonio\Payments\Dto\Response\Refund;
use Veltix\Montonio\Payments\Enum\PaymentStatus;
use Veltix\Montonio\Payments\Enum\RefundStatus;
use Veltix\Montonio\Payments\Enum\RefundType;

function fullOrderFixture(): array
{
    return [
        'uuid' => 'order-uuid-123',
        'paymentStatus' => 'PAID',
        'locale' => 'en',
        'merchantReference' => 'ref-001',
        'merchantReferenceDisplay' => 'REF-001',
        'merchantReturnUrl' => 'https://example.com/return',
        'merchantNotificationUrl' => 'https://example.com/notify',
        'grandTotal' => '150.00',
        'currency' => 'EUR',
        'paymentMethodType' => 'paymentInitiation',
        'storeUuid' => 'store-uuid-1',
        'paymentIntents' => [
            [
                'uuid' => 'pi-1',
                'paymentMethodType' => 'paymentInitiation',
                'paymentMethodMetadata' => ['provider' => 'SWEDBANK'],
                'amount' => '150.00',
                'currency' => 'EUR',
                'status' => 'PAID',
                'serviceFee' => '0.50',
                'serviceFeeCurrency' => 'EUR',
                'createdAt' => '2025-01-01T12:00:00Z',
            ],
        ],
        'refunds' => [
            [
                'uuid' => 'ref-1',
                'amount' => '25.00',
                'status' => 'SUCCESSFUL',
                'currency' => 'EUR',
                'createdAt' => '2025-01-02T12:00:00Z',
                'type' => 'PARTIAL_REFUND',
            ],
        ],
        'availableForRefund' => 125.00,
        'isRefundableType' => true,
        'lineItems' => [
            ['name' => 'Widget', 'quantity' => 3, 'finalPrice' => 50.00],
        ],
        'billingAddress' => ['firstName' => 'John', 'country' => 'EE'],
        'shippingAddress' => ['firstName' => 'Jane', 'country' => 'LT'],
        'expiresAt' => '2025-01-02T12:00:00Z',
        'createdAt' => '2025-01-01T10:00:00Z',
        'storeName' => 'Test Store',
        'businessName' => 'Test Business OÜ',
        'paymentUrl' => 'https://pay.montonio.com/order-uuid-123',
    ];
}

test('fromArray maps basic fields', function () {
    $response = OrderResponse::fromArray(fullOrderFixture());

    expect($response->uuid)->toBe('order-uuid-123')
        ->and($response->grandTotal)->toBe('150.00')
        ->and($response->currency)->toBe('EUR')
        ->and($response->merchantReference)->toBe('ref-001')
        ->and($response->createdAt)->toBe('2025-01-01T10:00:00Z');
});

test('maps PaymentStatus enum', function () {
    $response = OrderResponse::fromArray(fullOrderFixture());

    expect($response->paymentStatus)->toBe(PaymentStatus::PAID);
});

test('maps PaymentIntent array', function () {
    $response = OrderResponse::fromArray(fullOrderFixture());

    expect($response->paymentIntents)->toHaveCount(1);

    $pi = $response->paymentIntents[0];
    expect($pi)->toBeInstanceOf(PaymentIntent::class)
        ->and($pi->uuid)->toBe('pi-1')
        ->and($pi->paymentMethodType)->toBe('paymentInitiation')
        ->and($pi->paymentMethodMetadata)->toBe(['provider' => 'SWEDBANK'])
        ->and($pi->amount)->toBe('150.00')
        ->and($pi->status)->toBe('PAID')
        ->and($pi->serviceFee)->toBe('0.50')
        ->and($pi->serviceFeeCurrency)->toBe('EUR');
});

test('maps Refund array with enums', function () {
    $response = OrderResponse::fromArray(fullOrderFixture());

    expect($response->refunds)->toHaveCount(1);

    $refund = $response->refunds[0];
    expect($refund)->toBeInstanceOf(Refund::class)
        ->and($refund->uuid)->toBe('ref-1')
        ->and($refund->amount)->toBe('25.00')
        ->and($refund->status)->toBe(RefundStatus::SUCCESSFUL)
        ->and($refund->type)->toBe(RefundType::PARTIAL_REFUND);
});

test('maps Address DTOs', function () {
    $response = OrderResponse::fromArray(fullOrderFixture());

    expect($response->billingAddress)->toBeInstanceOf(Address::class)
        ->and($response->billingAddress->firstName)->toBe('John')
        ->and($response->shippingAddress)->toBeInstanceOf(Address::class)
        ->and($response->shippingAddress->firstName)->toBe('Jane');
});

test('maps LineItem array', function () {
    $response = OrderResponse::fromArray(fullOrderFixture());

    expect($response->lineItems)->toHaveCount(1);

    $item = $response->lineItems[0];
    expect($item)->toBeInstanceOf(LineItem::class)
        ->and($item->name)->toBe('Widget')
        ->and($item->quantity)->toBe(3)
        ->and($item->finalPrice)->toBe(50.00);
});

test('fromArray with docs order-created fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/order-created.json');
    $response = OrderResponse::fromArray($data);

    expect($response->uuid)->toBe('12228dce-2f7c-4db5-8d28-5d82a19aa3b6')
        ->and($response->paymentStatus)->toBe(PaymentStatus::PENDING)
        ->and($response->locale)->toBe('et')
        ->and($response->merchantReference)->toBe('MY-ORDER-ID-123')
        ->and($response->grandTotal)->toBe('99.99')
        ->and($response->currency)->toBe('EUR')
        ->and($response->paymentMethodType)->toBe('paymentInitiation')
        ->and($response->storeUuid)->toBe('703a60eb-ed19-4fc3-8a62-4474150a962a')
        ->and($response->paymentIntents)->toHaveCount(1)
        ->and($response->paymentIntents[0]->uuid)->toBe('302f259c-7501-49ed-b3aa-39a4bbcafec1')
        ->and($response->paymentIntents[0]->amount)->toBe('99.99')
        ->and($response->paymentIntents[0]->status)->toBe('PENDING')
        ->and($response->paymentIntents[0]->serviceFee)->toBeNull()
        ->and($response->refunds)->toBe([])
        ->and($response->lineItems)->toHaveCount(1)
        ->and($response->lineItems[0]->name)->toBe('Hoverboard')
        ->and($response->billingAddress->firstName)->toBe('CustomerFirst')
        ->and($response->shippingAddress->firstName)->toBe('CustomerFirstShipping')
        ->and($response->paymentUrl)->toBe('https://stargate.montonio.com/e694c95b-0335-493d-a713-af95720f885d');
});

test('fromArray with docs order-with-refunds fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Payments/order-with-refunds.json');
    $response = OrderResponse::fromArray($data);

    expect($response->uuid)->toBe('0ac2124d-9f8e-4a29-816d-7eef5b9bb0fd')
        ->and($response->paymentStatus)->toBe(PaymentStatus::PARTIALLY_REFUNDED)
        ->and($response->paymentMethodType)->toBe('cardPayments')
        ->and($response->refunds)->toHaveCount(2)
        ->and($response->refunds[0]->uuid)->toBe('92b11684-319a-4cce-92f5-56d348aa986a')
        ->and($response->refunds[0]->amount)->toBe('25')
        ->and($response->refunds[0]->status)->toBe(RefundStatus::SUCCESSFUL)
        ->and($response->refunds[0]->type)->toBe(RefundType::PARTIAL_REFUND)
        ->and($response->refunds[1]->uuid)->toBe('8453465a-a9d8-469e-a838-5b2b5b20f429')
        ->and($response->availableForRefund)->toBe(50.0)
        ->and($response->paymentUrl)->toBeNull();
});

test('handles null and optional fields', function () {
    $data = [
        'uuid' => 'order-min',
        'paymentStatus' => 'PENDING',
        'merchantReference' => 'ref',
        'grandTotal' => '10.00',
        'currency' => 'EUR',
        'createdAt' => '2025-01-01T00:00:00Z',
    ];

    $response = OrderResponse::fromArray($data);

    expect($response->locale)->toBeNull()
        ->and($response->merchantReferenceDisplay)->toBeNull()
        ->and($response->paymentMethodType)->toBeNull()
        ->and($response->storeUuid)->toBeNull()
        ->and($response->paymentIntents)->toBe([])
        ->and($response->refunds)->toBe([])
        ->and($response->availableForRefund)->toBe(0.0)
        ->and($response->isRefundableType)->toBeFalse()
        ->and($response->lineItems)->toBeNull()
        ->and($response->billingAddress)->toBeNull()
        ->and($response->shippingAddress)->toBeNull()
        ->and($response->expiresAt)->toBeNull()
        ->and($response->paymentUrl)->toBeNull();
});
