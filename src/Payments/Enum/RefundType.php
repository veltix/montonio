<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum RefundType: string
{
    case PARTIAL_REFUND = 'PARTIAL_REFUND';
    case FULL_REFUND = 'FULL_REFUND';
}
