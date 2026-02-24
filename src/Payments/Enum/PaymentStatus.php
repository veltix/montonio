<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case VOIDED = 'VOIDED';
    case PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    case REFUNDED = 'REFUNDED';
    case ABANDONED = 'ABANDONED';
    case AUTHORIZED = 'AUTHORIZED';
}
