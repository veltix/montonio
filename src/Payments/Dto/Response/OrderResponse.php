<?php

declare(strict_types=1);

namespace Veltix\Montonio\Payments\Dto\Response;

use Veltix\Montonio\Payments\Dto\Request\Address;
use Veltix\Montonio\Payments\Dto\Request\LineItem;
use Veltix\Montonio\Payments\Enum\PaymentStatus;

final readonly class OrderResponse
{
    /**
     * @param  PaymentIntent[]  $paymentIntents
     * @param  Refund[]  $refunds
     * @param  LineItem[]|null  $lineItems
     */
    public function __construct(
        public string $uuid,
        public PaymentStatus $paymentStatus,
        public ?string $locale,
        public string $merchantReference,
        public ?string $merchantReferenceDisplay,
        public ?string $merchantReturnUrl,
        public ?string $merchantNotificationUrl,
        public string $grandTotal,
        public string $currency,
        public ?string $paymentMethodType,
        public ?string $storeUuid,
        public array $paymentIntents,
        public array $refunds,
        public float $availableForRefund,
        public bool $isRefundableType,
        public ?array $lineItems,
        public ?Address $billingAddress,
        public ?Address $shippingAddress,
        public ?string $expiresAt,
        public string $createdAt,
        public ?string $storeName,
        public ?string $businessName,
        public ?string $paymentUrl,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            paymentStatus: PaymentStatus::from($data['paymentStatus']),
            locale: $data['locale'] ?? null,
            merchantReference: $data['merchantReference'],
            merchantReferenceDisplay: $data['merchantReferenceDisplay'] ?? null,
            merchantReturnUrl: $data['merchantReturnUrl'] ?? null,
            merchantNotificationUrl: $data['merchantNotificationUrl'] ?? null,
            grandTotal: (string) $data['grandTotal'],
            currency: $data['currency'],
            paymentMethodType: $data['paymentMethodType'] ?? null,
            storeUuid: $data['storeUuid'] ?? null,
            paymentIntents: array_map(
                fn (array $intent) => PaymentIntent::fromArray($intent),
                $data['paymentIntents'] ?? [],
            ),
            refunds: array_map(
                fn (array $refund) => Refund::fromArray($refund),
                $data['refunds'] ?? [],
            ),
            availableForRefund: (float) ($data['availableForRefund'] ?? 0),
            isRefundableType: (bool) ($data['isRefundableType'] ?? false),
            lineItems: isset($data['lineItems']) ? array_map(
                fn (array $item) => LineItem::fromArray($item),
                $data['lineItems'],
            ) : null,
            billingAddress: isset($data['billingAddress']) ? Address::fromArray($data['billingAddress']) : null,
            shippingAddress: isset($data['shippingAddress']) ? Address::fromArray($data['shippingAddress']) : null,
            expiresAt: $data['expiresAt'] ?? null,
            createdAt: $data['createdAt'],
            storeName: $data['storeName'] ?? null,
            businessName: $data['businessName'] ?? null,
            paymentUrl: $data['paymentUrl'] ?? null,
        );
    }
}
