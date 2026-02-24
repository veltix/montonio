<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum RefundStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case SUCCESSFUL = 'SUCCESSFUL';
    case REJECTED = 'REJECTED';
    case CANCELED = 'CANCELED';
}
