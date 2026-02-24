<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum PayoutExportType: string
{
    case Excel = 'excel';
    case Xml = 'xml';
}
