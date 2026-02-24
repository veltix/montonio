<?php

declare(strict_types=1);

namespace Veltix\Montonio\Shipping\Enum;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Registered = 'registered';
    case RegistrationFailed = 'registrationFailed';
    case LabelsCreated = 'labelsCreated';
    case InTransit = 'inTransit';
    case AwaitingCollection = 'awaitingCollection';
    case Delivered = 'delivered';
    case Returned = 'returned';
}
