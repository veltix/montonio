<?php

declare(strict_types=1);

use Veltix\Montonio\Auth\JwtFactory;
use Veltix\Montonio\Http\HttpClient;
use Veltix\Montonio\Http\PaymentsHttpClient;
use Veltix\Montonio\Payments\Dto\Request\CreateOrderRequest;
use Veltix\Montonio\Payments\Dto\Request\CreatePaymentLinkRequest;
use Veltix\Montonio\Payments\Dto\Request\CreateRefundRequest;
use Veltix\Montonio\Payments\Dto\Request\Payment;
use Veltix\Montonio\Payments\Dto\Response\OrderResponse;
use Veltix\Montonio\Payments\Dto\Response\PaymentLinkResponse;
use Veltix\Montonio\Payments\Dto\Response\PaymentMethodsResponse;
use Veltix\Montonio\Payments\Dto\Response\PayoutExportResponse;
use Veltix\Montonio\Payments\Dto\Response\PayoutsResponse;
use Veltix\Montonio\Payments\Dto\Response\RefundResponse;
use Veltix\Montonio\Payments\Dto\Response\SessionResponse;
use Veltix\Montonio\Payments\Dto\Response\StoreBalancesResponse;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;
use Veltix\Montonio\Payments\Enum\PaymentMethodCode;
use Veltix\Montonio\Payments\Enum\PayoutExportType;
use Veltix\Montonio\Payments\Enum\PayoutSortBy;
use Veltix\Montonio\Payments\Enum\PayoutSortOrder;
use Veltix\Montonio\Payments\PaymentsClient;

use function Veltix\Montonio\Tests\fixture;
use function Veltix\Montonio\Tests\jsonResponse;
use function Veltix\Montonio\Tests\mockPsrClient;
use function Veltix\Montonio\Tests\testConfig;

function paymentsClientWithResponse(array $responseData): array
{
    $mock = mockPsrClient(jsonResponse(200, $responseData));
    $config = testConfig($mock->client);
    $httpClient = new HttpClient($config);
    $jwtFactory = new JwtFactory($config);
    $paymentsHttp = new PaymentsHttpClient($httpClient, $jwtFactory, $config);
    $client = new PaymentsClient($paymentsHttp);

    return [$client, $mock];
}

test('getPaymentMethods returns PaymentMethodsResponse', function () {
    $fixtureData = fixture('Payments/payment-methods.json');
    $fixtureData['id'] = $fixtureData['uuid'];
    [$client, $mock] = paymentsClientWithResponse($fixtureData);

    $result = $client->getPaymentMethods();

    expect($result)->toBeInstanceOf(PaymentMethodsResponse::class)
        ->and($result->uuid)->toBe('0bafe86b-c5cf-4c88-ba28-484a8585f0f4')
        ->and($result->name)->toBe('Montonio Store')
        ->and($result->paymentMethods)->toHaveKey('paymentInitiation')
        ->and($result->paymentMethods)->toHaveKey('cardPayments')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/stores/payment-methods');
});

test('createOrder returns OrderResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/order-created.json'));

    $request = new CreateOrderRequest(
        merchantReference: 'ref-1',
        returnUrl: 'https://example.com/return',
        notificationUrl: 'https://example.com/notify',
        grandTotal: 100.00,
        currency: Currency::EUR,
        locale: Locale::EN,
        payment: new Payment(amount: 100.00, currency: Currency::EUR, method: PaymentMethodCode::PaymentInitiation),
    );

    $result = $client->createOrder($request);

    expect($result)->toBeInstanceOf(OrderResponse::class)
        ->and($result->uuid)->toBe('12228dce-2f7c-4db5-8d28-5d82a19aa3b6')
        ->and($mock->lastRequest()->getMethod())->toBe('POST')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/orders');
});

test('getOrder returns OrderResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/order-with-refunds.json'));

    $result = $client->getOrder('0ac2124d-9f8e-4a29-816d-7eef5b9bb0fd');

    expect($result)->toBeInstanceOf(OrderResponse::class)
        ->and($result->uuid)->toBe('0ac2124d-9f8e-4a29-816d-7eef5b9bb0fd')
        ->and($result->paymentStatus)->toBe(\Veltix\Montonio\Payments\Enum\PaymentStatus::PARTIALLY_REFUNDED)
        ->and($result->refunds)->toHaveCount(2)
        ->and((string) $mock->lastRequest()->getUri())->toContain('/orders/0ac2124d-9f8e-4a29-816d-7eef5b9bb0fd');
});

test('createRefund returns RefundResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/refund.json'));

    $request = new CreateRefundRequest(
        orderUuid: 'order-1',
        amount: 25.00,
        idempotencyKey: 'idem-1',
    );

    $result = $client->createRefund($request);

    expect($result)->toBeInstanceOf(RefundResponse::class)
        ->and($result->uuid)->toBe('97b20084-319a-4cce-92f5-56d3b41a986a')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/refunds');
});

test('createPaymentLink returns PaymentLinkResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/payment-link.json'));

    $request = new CreatePaymentLinkRequest(
        description: 'Test',
        currency: Currency::EUR,
        amount: 10.00,
        locale: Locale::EN,
        askAdditionalInfo: false,
        expiresAt: '2025-12-31T23:59:59Z',
    );

    $result = $client->createPaymentLink($request);

    expect($result)->toBeInstanceOf(PaymentLinkResponse::class)
        ->and($result->uuid)->toBe('1088b447-a9ab-42aa-b473-ea6ba174c671')
        ->and($result->url)->toBe('https://pay.montonio.com/1088b447-a9ab-42aa-b473-ea6ba174c671')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/payment-links');
});

test('createSession returns SessionResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/session.json'));

    $result = $client->createSession();

    expect($result)->toBeInstanceOf(SessionResponse::class)
        ->and($result->uuid)->toBe('087a9fb5-7a85-4e1e-b3f7-2546faab9a97')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/sessions');
});

test('listPayouts returns PayoutsResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/payouts.json'));

    $result = $client->listPayouts('b9ae2ec0-641c-421d-9a78-202bd59614d9', 10, 0, PayoutSortOrder::DESC);

    expect($result)->toBeInstanceOf(PayoutsResponse::class)
        ->and($result->payouts)->toHaveCount(1)
        ->and($result->payouts[0]->uuid)->toBe('671d9d42-7751-4a52-8734-d6eb250c3eea')
        ->and($result->payouts[0]->settlementType)->toBe('montonioMoneyMovement')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/stores/b9ae2ec0-641c-421d-9a78-202bd59614d9/payouts')
        ->and((string) $mock->lastRequest()->getUri())->toContain('limit=10')
        ->and((string) $mock->lastRequest()->getUri())->toContain('order=DESC');
});

test('listPayouts includes orderBy when provided', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/payouts.json'));

    $result = $client->listPayouts('store-1', 10, 0, PayoutSortOrder::ASC, PayoutSortBy::CreatedAt);

    expect($result)->toBeInstanceOf(PayoutsResponse::class)
        ->and((string) $mock->lastRequest()->getUri())->toContain('orderBy=createdAt');
});

test('getPayoutExport returns PayoutExportResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/payout-export.json'));

    $result = $client->getPayoutExport('store-1', 'payout-1', PayoutExportType::Excel);

    expect($result)->toBeInstanceOf(PayoutExportResponse::class)
        ->and($result->url)->toContain('.xlsx')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/stores/store-1/payouts/payout-1/export-excel');
});

test('getStoreBalances returns StoreBalancesResponse', function () {
    [$client, $mock] = paymentsClientWithResponse(fixture('Payments/store-balances.json'));

    $result = $client->getStoreBalances();

    expect($result)->toBeInstanceOf(StoreBalancesResponse::class)
        ->and($result->store->uuid)->toBe('74e498c8-8d80-4b79-a4ba-ae2c12bbe50d')
        ->and($result->store->name)->toBe('ShopName')
        ->and($result->balances)->toHaveKey('stripe')
        ->and($result->balances)->toHaveKey('montonioMoneyMovement')
        ->and((string) $mock->lastRequest()->getUri())->toContain('/store-balances');
});
