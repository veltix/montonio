<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Enum;

enum PaymentMethodCode: string
{
    case PaymentInitiation = 'paymentInitiation';
    case CardPayments = 'cardPayments';
    case Blik = 'blik';
    case Bnpl = 'bnpl';
    case HirePurchase = 'hirePurchase';
}
