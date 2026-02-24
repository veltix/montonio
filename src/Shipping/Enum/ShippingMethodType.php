<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum ShippingMethodType: string
{
    case Courier = 'courier';
    case PickupPoint = 'pickupPoint';
}
