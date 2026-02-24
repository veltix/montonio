<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\Address;
use Veltix\Montonio\Payments\Dto\Request\BlikOptions;
use Veltix\Montonio\Payments\Dto\Request\BnplOptions;
use Veltix\Montonio\Payments\Dto\Request\CardPaymentOptions;
use Veltix\Montonio\Payments\Dto\Request\CreateOrderRequest;
use Veltix\Montonio\Payments\Dto\Request\CreatePaymentLinkRequest;
use Veltix\Montonio\Payments\Dto\Request\CreateRefundRequest;
use Veltix\Montonio\Payments\Dto\Request\LineItem;
use Veltix\Montonio\Payments\Dto\Request\Payment;
use Veltix\Montonio\Payments\Dto\Request\PaymentInitiationOptions;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;
use Veltix\Montonio\Payments\Enum\PaymentMethodCode;
use Veltix\Montonio\Payments\Enum\PaymentStatus;
use Veltix\Montonio\Webhook\Dto\PaymentWebhookPayload;

use function Pest\Faker\fake;

test('Address with random data roundtrips', function () {
    $address = new Address(
        firstName: fake()->firstName(),
        lastName: fake()->lastName(),
        email: fake()->safeEmail(),
        phoneNumber: fake()->numerify('#######'),
        phoneCountry: fake()->numerify('+###'),
        addressLine1: fake()->streetAddress(),
        addressLine2: fake()->word(),
        locality: fake()->city(),
        region: fake()->word(),
        country: fake()->countryCode(),
        postalCode: fake()->postcode(),
        companyName: fake()->company(),
        companyLegalName: fake()->company(),
        companyRegCode: fake()->numerify('########'),
        companyVatNumber: fake()->numerify('##########'),
    );

    $array = $address->toArray();
    $restored = Address::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('LineItem with random data roundtrips', function () {
    $lineItem = new LineItem(
        name: fake()->name(),
        quantity: fake()->numberBetween(1, 100),
        finalPrice: fake()->randomFloat(2, 1, 9999),
    );

    $array = $lineItem->toArray();
    $restored = LineItem::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('Payment with random data roundtrips', function () {
    $payment = new Payment(
        amount: fake()->randomFloat(2, 1, 9999),
        currency: fake()->randomElement(Currency::cases()),
        method: PaymentMethodCode::PaymentInitiation,
        methodDisplay: fake()->word(),
        methodOptions: new PaymentInitiationOptions(
            preferredProvider: fake()->word(),
            preferredCountry: fake()->countryCode(),
        ),
    );

    $array = $payment->toArray();
    $restored = Payment::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('PaymentInitiationOptions with random data roundtrips', function () {
    $options = new PaymentInitiationOptions(
        preferredProvider: fake()->word(),
        preferredCountry: fake()->countryCode(),
        preferredLocale: fake()->languageCode(),
        paymentDescription: fake()->sentence(),
        paymentReference: fake()->uuid(),
    );

    $array = $options->toArray();
    $restored = PaymentInitiationOptions::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CardPaymentOptions with random data roundtrips', function () {
    $options = new CardPaymentOptions(
        preferredMethod: fake()->word(),
    );

    $array = $options->toArray();
    $restored = CardPaymentOptions::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('BlikOptions with random data roundtrips', function () {
    $options = new BlikOptions(
        preferredLocale: fake()->languageCode(),
        blikCode: fake()->numerify('######'),
    );

    $array = $options->toArray();
    $restored = BlikOptions::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('BnplOptions with random data roundtrips', function () {
    $options = new BnplOptions(
        period: fake()->numberBetween(1, 36),
    );

    $array = $options->toArray();
    $restored = BnplOptions::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CreateOrderRequest with random data roundtrips', function () {
    $request = new CreateOrderRequest(
        merchantReference: fake()->uuid(),
        returnUrl: fake()->url(),
        notificationUrl: fake()->url(),
        grandTotal: fake()->randomFloat(2, 1, 9999),
        currency: fake()->randomElement(Currency::cases()),
        locale: fake()->randomElement(Locale::cases()),
        payment: new Payment(
            amount: fake()->randomFloat(2, 1, 9999),
            currency: fake()->randomElement(Currency::cases()),
            method: PaymentMethodCode::CardPayments,
            methodOptions: new CardPaymentOptions(
                preferredMethod: fake()->word(),
            ),
        ),
        billingAddress: new Address(
            firstName: fake()->firstName(),
            lastName: fake()->lastName(),
            email: fake()->safeEmail(),
        ),
        shippingAddress: new Address(
            addressLine1: fake()->streetAddress(),
            locality: fake()->city(),
            country: fake()->countryCode(),
        ),
        lineItems: [
            new LineItem(
                name: fake()->word(),
                quantity: fake()->numberBetween(1, 10),
                finalPrice: fake()->randomFloat(2, 1, 999),
            ),
        ],
        expiresIn: fake()->numberBetween(60, 3600),
        sessionUuid: fake()->uuid(),
    );

    $array = $request->toArray();
    $restored = CreateOrderRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CreateRefundRequest with random data roundtrips', function () {
    $request = new CreateRefundRequest(
        orderUuid: fake()->uuid(),
        amount: fake()->randomFloat(2, 1, 9999),
        idempotencyKey: fake()->uuid(),
    );

    $array = $request->toArray();
    $restored = CreateRefundRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('CreatePaymentLinkRequest with random data roundtrips', function () {
    $request = new CreatePaymentLinkRequest(
        description: fake()->sentence(),
        currency: fake()->randomElement(Currency::cases()),
        amount: fake()->randomFloat(2, 1, 9999),
        locale: fake()->randomElement(Locale::cases()),
        askAdditionalInfo: fake()->boolean(),
        expiresAt: fake()->iso8601(),
        type: fake()->word(),
        notificationUrl: fake()->url(),
        returnUrl: fake()->url(),
        preferredProvider: fake()->word(),
        preferredCountry: fake()->countryCode(),
        merchantReference: fake()->uuid(),
        paymentReference: fake()->uuid(),
    );

    $array = $request->toArray();
    $restored = CreatePaymentLinkRequest::fromArray($array);

    expect($restored->toArray())->toBe($array);
});

test('PaymentWebhookPayload with random data', function () {
    $data = (object) [
        'uuid' => fake()->uuid(),
        'accessKey' => fake()->uuid(),
        'merchantReference' => fake()->uuid(),
        'merchantReferenceDisplay' => fake()->word(),
        'paymentStatus' => fake()->randomElement(PaymentStatus::cases())->value,
        'paymentMethod' => fake()->word(),
        'grandTotal' => fake()->randomFloat(2, 1, 9999),
        'currency' => fake()->randomElement(Currency::cases())->value,
        'senderIban' => fake()->iban(),
        'senderName' => fake()->name(),
        'paymentProviderName' => fake()->company(),
        'paymentLinkUuid' => fake()->uuid(),
        'iat' => fake()->unixTime(),
        'exp' => fake()->unixTime(),
    ];

    $payload = PaymentWebhookPayload::fromObject($data);

    expect($payload->uuid)->toBe($data->uuid)
        ->and($payload->accessKey)->toBe($data->accessKey)
        ->and($payload->merchantReference)->toBe($data->merchantReference)
        ->and($payload->paymentStatus)->toBe(PaymentStatus::from($data->paymentStatus))
        ->and($payload->grandTotal)->toBe((float) $data->grandTotal)
        ->and($payload->currency)->toBe($data->currency);
});

test('multiple random iterations produce valid DTOs', function () {
    for ($i = 0; $i < 5; $i++) {
        $request = new CreateOrderRequest(
            merchantReference: fake()->uuid(),
            returnUrl: fake()->url(),
            notificationUrl: fake()->url(),
            grandTotal: fake()->randomFloat(2, 1, 9999),
            currency: fake()->randomElement(Currency::cases()),
            locale: fake()->randomElement(Locale::cases()),
            payment: new Payment(
                amount: fake()->randomFloat(2, 1, 9999),
                currency: fake()->randomElement(Currency::cases()),
                method: fake()->randomElement(PaymentMethodCode::cases()),
            ),
        );

        $array = $request->toArray();
        $restored = CreateOrderRequest::fromArray($array);

        expect($restored->toArray())->toBe($array);
    }
});
