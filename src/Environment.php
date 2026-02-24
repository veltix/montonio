<?php

declare(strict_types=1);

namespace Veltix\Montonio;

enum Environment: string
{
    case Production = 'production';
    case Sandbox = 'sandbox';

    public function paymentsBaseUrl(): string
    {
        return match ($this) {
            self::Production => 'https://stargate.montonio.com/api',
            self::Sandbox => 'https://sandbox-stargate.montonio.com/api',
        };
    }

    public function shippingBaseUrl(): string
    {
        return match ($this) {
            self::Production => 'https://shipping.montonio.com/api/v2',
            self::Sandbox => 'https://sandbox-shipping.montonio.com/api/v2',
        };
    }
}
