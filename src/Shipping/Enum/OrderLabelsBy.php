<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum OrderLabelsBy: string
{
    case Carrier = 'carrier';
    case CreatedAt = 'createdAt';
}
