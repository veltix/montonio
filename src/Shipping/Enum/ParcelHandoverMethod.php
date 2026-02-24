<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum ParcelHandoverMethod: string
{
    case CourierPickUp = 'courierPickUp';
    case TerminalDropOff = 'terminalDropOff';
}
