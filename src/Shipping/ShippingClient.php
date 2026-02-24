<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping;

use Veltix\Montonio\Http\ShippingHttpClient;
use Veltix\Montonio\Shipping\Dto\Request\CreateLabelFileRequest;
use Veltix\Montonio\Shipping\Dto\Request\CreateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Request\CreateWebhookRequest;
use Veltix\Montonio\Shipping\Dto\Request\FilterByParcelsRequest;
use Veltix\Montonio\Shipping\Dto\Request\ShippingRatesRequest;
use Veltix\Montonio\Shipping\Dto\Request\UpdateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Response\CarriersResponse;
use Veltix\Montonio\Shipping\Dto\Response\CourierServicesResponse;
use Veltix\Montonio\Shipping\Dto\Response\LabelFileResponse;
use Veltix\Montonio\Shipping\Dto\Response\PickupPointsResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShipmentResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShippingMethodsResponse;
use Veltix\Montonio\Shipping\Dto\Response\ShippingRatesResponse;
use Veltix\Montonio\Shipping\Dto\Response\WebhookListResponse;
use Veltix\Montonio\Shipping\Dto\Response\WebhookResponse;
use Veltix\Montonio\Shipping\Enum\PickupPointSubtype;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;

final readonly class ShippingClient
{
    public function __construct(
        private ShippingHttpClient $httpClient,
    ) {}

    public function getCarriers(): CarriersResponse
    {
        $data = $this->httpClient->get('/carriers');

        return CarriersResponse::fromArray($data);
    }

    public function getShippingMethods(): ShippingMethodsResponse
    {
        $data = $this->httpClient->get('/shipping-methods');

        return ShippingMethodsResponse::fromArray($data);
    }

    public function getPickupPoints(
        string $carrierCode,
        string $countryCode,
        ?PickupPointSubtype $type = null,
    ): PickupPointsResponse {
        $params = [
            'carrierCode' => $carrierCode,
            'countryCode' => $countryCode,
        ];

        if ($type !== null) {
            $params['type'] = $type->value;
        }

        $data = $this->httpClient->get('/shipping-methods/pickup-points', $params);

        return PickupPointsResponse::fromArray($data);
    }

    public function getCourierServices(
        string $carrierCode,
        string $countryCode,
    ): CourierServicesResponse {
        $data = $this->httpClient->get('/shipping-methods/courier-services', [
            'carrierCode' => $carrierCode,
            'countryCode' => $countryCode,
        ]);

        return CourierServicesResponse::fromArray($data);
    }

    public function filterShippingMethodsByParcels(
        string $destination,
        FilterByParcelsRequest $request,
        ?string $source = null,
    ): ShippingMethodsResponse {
        $queryParams = ['destination' => $destination];

        if ($source !== null) {
            $queryParams['source'] = $source;
        }

        $data = $this->httpClient->post(
            '/shipping-methods/filter-by-parcels',
            $request->toArray(),
            $queryParams,
        );

        return ShippingMethodsResponse::fromArray($data);
    }

    public function getShippingRates(
        ShippingRatesRequest $request,
        ?string $carrierCode = null,
        ?ShippingMethodType $shippingMethodType = null,
    ): ShippingRatesResponse {
        $queryParams = [];

        if ($carrierCode !== null) {
            $queryParams['carrierCode'] = $carrierCode;
        }

        if ($shippingMethodType !== null) {
            $queryParams['shippingMethodType'] = $shippingMethodType->value;
        }

        $data = $this->httpClient->post(
            '/shipping-methods/rates',
            $request->toArray(),
            $queryParams,
        );

        return ShippingRatesResponse::fromArray($data);
    }

    public function createShipment(CreateShipmentRequest $request): ShipmentResponse
    {
        $data = $this->httpClient->post('/shipments', $request->toArray());

        return ShipmentResponse::fromArray($data);
    }

    public function updateShipment(string $shipmentId, UpdateShipmentRequest $request): ShipmentResponse
    {
        $data = $this->httpClient->patch('/shipments/'.$shipmentId, $request->toArray());

        return ShipmentResponse::fromArray($data);
    }

    public function getShipment(string $shipmentId): ShipmentResponse
    {
        $data = $this->httpClient->get('/shipments/'.$shipmentId);

        return ShipmentResponse::fromArray($data);
    }

    public function createLabelFile(CreateLabelFileRequest $request): LabelFileResponse
    {
        $data = $this->httpClient->post('/label-files', $request->toArray());

        return LabelFileResponse::fromArray($data);
    }

    public function getLabelFile(string $labelFileId): LabelFileResponse
    {
        $data = $this->httpClient->get('/label-files/'.$labelFileId);

        return LabelFileResponse::fromArray($data);
    }

    public function createWebhook(CreateWebhookRequest $request): WebhookResponse
    {
        $data = $this->httpClient->post('/webhooks', $request->toArray());

        return WebhookResponse::fromArray($data);
    }

    public function listWebhooks(): WebhookListResponse
    {
        $data = $this->httpClient->get('/webhooks');

        return WebhookListResponse::fromArray($data);
    }

    public function deleteWebhook(string $webhookId): void
    {
        $this->httpClient->delete('/webhooks/'.$webhookId);
    }
}
