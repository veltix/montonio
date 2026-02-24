<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum LabelFileStatus: string
{
    case Pending = 'pending';
    case Ready = 'ready';
    case Failed = 'failed';
}
