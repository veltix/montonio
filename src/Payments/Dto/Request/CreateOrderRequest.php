<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Request;

use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;

final readonly class CreateOrderRequest
{
    /**
     * @param  LineItem[]|null  $lineItems
     */
    public function __construct(
        public string $merchantReference,
        public string $returnUrl,
        public string $notificationUrl,
        public float $grandTotal,
        public Currency $currency,
        public Locale $locale,
        public Payment $payment,
        public ?Address $billingAddress = null,
        public ?Address $shippingAddress = null,
        public ?array $lineItems = null,
        public ?int $expiresIn = null,
        public ?string $sessionUuid = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'merchantReference' => $this->merchantReference,
            'returnUrl' => $this->returnUrl,
            'notificationUrl' => $this->notificationUrl,
            'grandTotal' => $this->grandTotal,
            'currency' => $this->currency->value,
            'locale' => $this->locale->value,
            'payment' => $this->payment->toArray(),
        ];

        if ($this->billingAddress !== null) {
            $data['billingAddress'] = $this->billingAddress->toArray();
        }

        if ($this->shippingAddress !== null) {
            $data['shippingAddress'] = $this->shippingAddress->toArray();
        }

        if ($this->lineItems !== null) {
            $data['lineItems'] = array_map(
                fn (LineItem $item) => $item->toArray(),
                $this->lineItems,
            );
        }

        if ($this->expiresIn !== null) {
            $data['expiresIn'] = $this->expiresIn;
        }

        if ($this->sessionUuid !== null) {
            $data['sessionUuid'] = $this->sessionUuid;
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            merchantReference: $data['merchantReference'],
            returnUrl: $data['returnUrl'],
            notificationUrl: $data['notificationUrl'],
            grandTotal: (float) $data['grandTotal'],
            currency: Currency::from($data['currency']),
            locale: Locale::from($data['locale']),
            payment: Payment::fromArray($data['payment']),
            billingAddress: isset($data['billingAddress']) ? Address::fromArray($data['billingAddress']) : null,
            shippingAddress: isset($data['shippingAddress']) ? Address::fromArray($data['shippingAddress']) : null,
            lineItems: isset($data['lineItems']) ? array_map(
                fn (array $item) => LineItem::fromArray($item),
                $data['lineItems'],
            ) : null,
            expiresIn: $data['expiresIn'] ?? null,
            sessionUuid: $data['sessionUuid'] ?? null,
        );
    }
}
