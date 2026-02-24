<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum PayoutSortBy: string
{
    case CreatedAt = 'createdAt';
    case SettlementType = 'settlementType';
    case TotalAmount = 'totalAmount';
    case Status = 'status';
}
