<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum ContractType: string
{
    case DIRECT = 'DIRECT';
    case MONTONIO = 'MONTONIO';
}
