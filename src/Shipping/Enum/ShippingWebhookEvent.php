<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum ShippingWebhookEvent: string
{
    case ShipmentRegistered = 'shipment.registered';
    case ShipmentRegistrationFailed = 'shipment.registrationFailed';
    case ShipmentLabelsCreated = 'shipment.labelsCreated';
    case ShipmentStatusUpdated = 'shipment.statusUpdated';
    case LabelFileReady = 'labelFile.ready';
    case LabelFileCreationFailed = 'labelFile.creationFailed';
}
