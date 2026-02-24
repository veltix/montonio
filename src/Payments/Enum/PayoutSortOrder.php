<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum PayoutSortOrder: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
