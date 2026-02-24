<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments;

use Veltix\Montonio\Http\PaymentsHttpClient;
use Veltix\Montonio\Payments\Dto\Request\CreateOrderRequest;
use Veltix\Montonio\Payments\Dto\Request\CreatePaymentLinkRequest;
use Veltix\Montonio\Payments\Dto\Request\CreateRefundRequest;
use Veltix\Montonio\Payments\Dto\Response\OrderResponse;
use Veltix\Montonio\Payments\Dto\Response\PaymentLinkResponse;
use Veltix\Montonio\Payments\Dto\Response\PaymentMethodsResponse;
use Veltix\Montonio\Payments\Dto\Response\PayoutExportResponse;
use Veltix\Montonio\Payments\Dto\Response\PayoutsResponse;
use Veltix\Montonio\Payments\Dto\Response\RefundResponse;
use Veltix\Montonio\Payments\Dto\Response\SessionResponse;
use Veltix\Montonio\Payments\Dto\Response\StoreBalancesResponse;
use Veltix\Montonio\Payments\Enum\PayoutExportType;
use Veltix\Montonio\Payments\Enum\PayoutSortBy;
use Veltix\Montonio\Payments\Enum\PayoutSortOrder;

final readonly class PaymentsClient
{
    public function __construct(
        private PaymentsHttpClient $httpClient,
    ) {}

    public function getPaymentMethods(): PaymentMethodsResponse
    {
        $data = $this->httpClient->get('/stores/payment-methods');

        return PaymentMethodsResponse::fromArray($data);
    }

    public function createOrder(CreateOrderRequest $request): OrderResponse
    {
        $data = $this->httpClient->post('/orders', $request->toArray());

        return OrderResponse::fromArray($data);
    }

    public function getOrder(string $orderUuid): OrderResponse
    {
        $data = $this->httpClient->get('/orders/'.$orderUuid);

        return OrderResponse::fromArray($data);
    }

    public function createRefund(CreateRefundRequest $request): RefundResponse
    {
        $data = $this->httpClient->post('/refunds', $request->toArray());

        return RefundResponse::fromArray($data);
    }

    public function createPaymentLink(CreatePaymentLinkRequest $request): PaymentLinkResponse
    {
        $data = $this->httpClient->post('/payment-links', $request->toArray());

        return PaymentLinkResponse::fromArray($data);
    }

    public function createSession(): SessionResponse
    {
        $data = $this->httpClient->post('/sessions');

        return SessionResponse::fromArray($data);
    }

    public function listPayouts(
        string $storeUuid,
        int $limit,
        int $offset,
        PayoutSortOrder $order,
        ?PayoutSortBy $orderBy = null,
    ): PayoutsResponse {
        $path = '/stores/'.$storeUuid.'/payouts?limit='.$limit.'&offset='.$offset.'&order='.$order->value;

        if ($orderBy !== null) {
            $path .= '&orderBy='.$orderBy->value;
        }

        $data = $this->httpClient->get($path);

        return PayoutsResponse::fromArray($data);
    }

    public function getPayoutExport(
        string $storeUuid,
        string $payoutUuid,
        PayoutExportType $type,
    ): PayoutExportResponse {
        $data = $this->httpClient->get(
            '/stores/'.$storeUuid.'/payouts/'.$payoutUuid.'/export-'.$type->value,
        );

        return PayoutExportResponse::fromArray($data);
    }

    public function getStoreBalances(): StoreBalancesResponse
    {
        $data = $this->httpClient->get('/store-balances');

        return StoreBalancesResponse::fromArray($data);
    }
}
