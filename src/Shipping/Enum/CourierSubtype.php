<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum CourierSubtype: string
{
    case Standard = 'standard';
    case StandardB2B = 'standardB2B';
}
