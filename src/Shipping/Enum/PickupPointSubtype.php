<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum PickupPointSubtype: string
{
    case ParcelMachine = 'parcelMachine';
    case PostOffice = 'postOffice';
    case ParcelShop = 'parcelShop';
}
