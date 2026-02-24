# Montonio PHP SDK

A strictly-typed PHP SDK for the [Montonio](https://montonio.com) Payments and Shipping APIs. Built on PSR-18 HTTP clients with immutable DTOs, JWT authentication, and comprehensive webhook verification.

## Features

- **Payments API** — Create orders, refunds, payment links, sessions; retrieve payment methods, payouts, store balances
- **Shipping API** — Create/update shipments, generate labels, manage webhooks; query carriers, shipping methods, pickup points, courier services, shipping rates
- **Webhook Verification** — JWT-based verification for both payment and shipping webhook payloads
- **Immutable DTOs** — All request and response objects are `final readonly` classes
- **String-Backed Enums** — Payment statuses, currencies, locales, shipping methods, and more
- **PSR Compliant** — Uses PSR-7 (HTTP Messages), PSR-17 (HTTP Factories), and PSR-18 (HTTP Client)
- **Exception Hierarchy** — Typed exceptions mapped to HTTP status codes
- **Environment Switching** — First-class sandbox and production support

## Requirements

- PHP **^8.2**
- A PSR-18 HTTP client implementation (e.g., Guzzle, Symfony HttpClient)
- A PSR-17 HTTP factory implementation
- Montonio API credentials (`accessKey` and `secretKey`)

## Installation

```bash
composer require veltix/montonio
```

You must also install a PSR-18 HTTP client and PSR-17 factory. For example, with Guzzle:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

Or with Symfony:

```bash
composer require symfony/http-client nyholm/psr7
```

## Configuration

The SDK is configured through the `Veltix\Montonio\Config` class:

```php
use Veltix\Montonio\Config;
use Veltix\Montonio\Environment;
use Veltix\Montonio\Montonio;

$config = new Config(
    accessKey: 'your-access-key',
    secretKey: 'your-secret-key',
    environment: Environment::Sandbox, // or Environment::Production
    httpClient: $psrHttpClient,        // PSR-18 ClientInterface
    requestFactory: $requestFactory,   // PSR-17 RequestFactoryInterface
    streamFactory: $streamFactory,     // PSR-17 StreamFactoryInterface
    jwtTtl: 3600,                      // Optional: JWT lifetime in seconds (default: 3600)
    jwtLeeway: 300,                    // Optional: JWT clock skew tolerance (default: 300)
);

$montonio = new Montonio($config);
```

The `Environment` enum provides two modes with separate base URLs:

| Environment | Payments API | Shipping API |
|---|---|---|
| `Environment::Production` | `https://stargate.montonio.com/api` | `https://shipping.montonio.com/api/v2` |
| `Environment::Sandbox` | `https://sandbox-stargate.montonio.com/api` | `https://sandbox-shipping.montonio.com/api/v2` |

## Usage

### Create a Payment Order

```php
use Veltix\Montonio\Payments\Dto\Request\CreateOrderRequest;
use Veltix\Montonio\Payments\Dto\Request\Payment;
use Veltix\Montonio\Payments\Dto\Request\LineItem;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;
use Veltix\Montonio\Payments\Enum\PaymentMethodCode;

$order = $montonio->payments()->createOrder(new CreateOrderRequest(
    merchantReference: 'ORDER-123',
    returnUrl: 'https://example.com/return',
    notificationUrl: 'https://example.com/webhook',
    grandTotal: 29.99,
    currency: Currency::EUR,
    locale: Locale::EN,
    payment: new Payment(
        amount: 29.99,
        currency: Currency::EUR,
        method: PaymentMethodCode::PaymentInitiation,
    ),
    lineItems: [
        new LineItem(name: 'Product A', quantity: 1, finalPrice: 29.99),
    ],
));

// Redirect the customer to the payment URL
$paymentUrl = $order->paymentUrl;
```

### Retrieve Payment Methods

```php
$response = $montonio->payments()->getPaymentMethods();

// Iterate over each payment method type (e.g. paymentInitiation, cardPayments, bnpl)
foreach ($response->paymentMethods as $methodCode => $detail) {
    echo $detail->processor; // e.g. "paymentInitiation"
    echo $detail->logoUrl;   // Logo URL for this payment method

    // Each method has a setup keyed by country code
    foreach ($detail->setup ?? [] as $countryCode => $country) {
        echo $countryCode; // e.g. "EE", "LT", "LV"

        // Supported currencies for this country
        $country->supportedCurrencies; // e.g. ["EUR"]

        // Individual bank/provider options
        foreach ($country->paymentMethods as $bank) {
            echo $bank->code;    // e.g. "LHVBEE22"
            echo $bank->name;    // e.g. "LHV"
            echo $bank->logoUrl; // Bank logo URL
            $bank->supportedCurrencies; // ["EUR"]
            $bank->uiPosition;          // Display order (nullable)
        }
    }
}
```

### Create a Refund

```php
use Veltix\Montonio\Payments\Dto\Request\CreateRefundRequest;

$refund = $montonio->payments()->createRefund(new CreateRefundRequest(
    orderUuid: $order->uuid,
    amount: 10.00,
    idempotencyKey: 'refund-unique-key',
));
```

### Create a Payment Link

```php
use Veltix\Montonio\Payments\Dto\Request\CreatePaymentLinkRequest;
use Veltix\Montonio\Payments\Enum\Currency;
use Veltix\Montonio\Payments\Enum\Locale;

$link = $montonio->payments()->createPaymentLink(new CreatePaymentLinkRequest(
    description: 'Invoice #456',
    currency: Currency::EUR,
    amount: 50.00,
    locale: Locale::EN,
    askAdditionalInfo: false,
    expiresAt: '2025-12-31T23:59:59Z',
));

$paymentLinkUrl = $link->url;
```

### Create a Shipment

```php
use Veltix\Montonio\Shipping\Dto\Request\CreateShipmentRequest;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentShippingMethod;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentReceiver;
use Veltix\Montonio\Shipping\Dto\Request\ShipmentParcel;
use Veltix\Montonio\Shipping\Enum\ShippingMethodType;

$shipment = $montonio->shipping()->createShipment(new CreateShipmentRequest(
    shippingMethod: new ShipmentShippingMethod(
        type: ShippingMethodType::PickupPoint,
        id: 'shipping-method-uuid',
    ),
    receiver: new ShipmentReceiver(
        name: 'Jane Doe',
        phoneCountryCode: '+372',
        phoneNumber: '55512345',
        email: 'jane@example.com',
    ),
    parcels: [
        new ShipmentParcel(weight: 1.5),
    ],
    merchantReference: 'SHIP-789',
));
```

### Generate Shipping Labels

```php
use Veltix\Montonio\Shipping\Dto\Request\CreateLabelFileRequest;
use Veltix\Montonio\Shipping\Enum\PageSize;

$labelFile = $montonio->shipping()->createLabelFile(new CreateLabelFileRequest(
    shipmentIds: [$shipment->id],
    pageSize: PageSize::A4,
));

// Poll for label readiness
$labelFile = $montonio->shipping()->getLabelFile($labelFile->id);
```

### Verify a Payment Webhook

```php
// The order token is received via your webhook endpoint
$payload = $montonio->webhooks()->verifyPaymentWebhook($orderToken);

$payload->uuid;              // Order UUID
$payload->paymentStatus;     // PaymentStatus enum
$payload->merchantReference; // Your merchant reference
$payload->grandTotal;        // Payment amount
```

### Verify a Shipping Webhook

```php
$payload = $montonio->webhooks()->verifyShippingWebhook($jwtPayload);

$payload->eventType;   // ShippingWebhookEvent enum
$payload->shipmentId;  // Shipment ID
$payload->eventId;     // Unique event ID
$payload->data;        // Event-specific data array
```

### List Payouts

```php
use Veltix\Montonio\Payments\Enum\PayoutSortOrder;
use Veltix\Montonio\Payments\Enum\PayoutSortBy;

$payouts = $montonio->payments()->listPayouts(
    storeUuid: $storeUuid,
    limit: 20,
    offset: 0,
    order: PayoutSortOrder::DESC,
    orderBy: PayoutSortBy::CreatedAt,
);
```

### Sync All Shipping Methods

Use `getShippingMethods()` to fetch every available carrier and shipping method in a single call. This is ideal for a daily sync to your database.

```php
$response = $montonio->shipping()->getShippingMethods();

foreach ($response->countries as $country) {
    echo $country->countryCode; // e.g. "EE", "LT", "LV", "FI"

    foreach ($country->carriers as $carrier) {
        echo $carrier->carrierCode; // e.g. "omniva", "dpd", "itella"

        foreach ($carrier->shippingMethods as $method) {
            echo $method->type; // "courier" or "pickupPoint"

            // Subtypes with optional rates
            foreach ($method->subtypes ?? [] as $subtype) {
                echo $subtype->code;     // e.g. "parcelMachine", "standard"
                echo $subtype->rate;     // Price as float (nullable)
                echo $subtype->currency; // e.g. "EUR" (nullable)
            }

            // Whether parcel dimensions are required for this method
            $method->constraints?->parcelDimensionsRequired; // bool
        }
    }
}
```

### Query Parcel Machines and Pickup Points

```php
use Veltix\Montonio\Shipping\Enum\PickupPointSubtype;

// Get all parcel machines for a carrier and country
$response = $montonio->shipping()->getPickupPoints(
    carrierCode: 'omniva',
    countryCode: 'EE',
    type: PickupPointSubtype::ParcelMachine,
);

foreach ($response->pickupPoints as $point) {
    echo $point->id;            // Pickup point ID (used when creating shipments)
    echo $point->name;          // e.g. "Tallinn Ülemiste"
    echo $point->type;          // e.g. "parcelMachine"
    echo $point->carrierCode;   // e.g. "omniva"

    // Location details
    echo $point->streetAddress; // e.g. "Suur-Sõjamäe 4"
    echo $point->locality;      // e.g. "Tallinn"
    echo $point->postalCode;    // e.g. "11415"

    // Additional services supported by this pickup point
    foreach ($point->additionalServices as $service) {
        echo $service->code; // e.g. "cod"
    }
}

// Other subtypes: ParcelShop, PostOffice
$postOffices = $montonio->shipping()->getPickupPoints(
    carrierCode: 'omniva',
    countryCode: 'EE',
    type: PickupPointSubtype::PostOffice,
);

// Omit type to get all pickup point types at once
$all = $montonio->shipping()->getPickupPoints(
    carrierCode: 'omniva',
    countryCode: 'EE',
);
```

### Query Courier Services

```php
$courierServices = $montonio->shipping()->getCourierServices(
    carrierCode: 'omniva',
    countryCode: 'EE',
);
```

## Architecture Overview

### Module Separation

The SDK is organized into clearly separated domains:

- **`Veltix\Montonio\Payments`** — Payment orders, refunds, payment links, sessions, payouts, store balances
- **`Veltix\Montonio\Shipping`** — Shipments, labels, carriers, shipping methods, pickup points, rates, webhooks
- **`Veltix\Montonio\Webhook`** — JWT-based webhook payload verification and deserialization
- **`Veltix\Montonio\Auth`** — JWT token creation (`JwtFactory`) and decoding (`JwtDecoder`)
- **`Veltix\Montonio\Http`** — PSR-compliant HTTP transport with domain-specific clients
- **`Veltix\Montonio\Exception`** — Structured exception hierarchy

The `Montonio` facade provides lazy-initialized access to `PaymentsClient`, `ShippingClient`, and `WebhookVerifier`.

### DTO Strategy

All DTOs follow these conventions:

- **`final readonly class`** — Immutable after construction
- **Named constructor parameters** — Clear, self-documenting construction
- **`toArray()` methods** on request DTOs for serialization
- **`fromArray()` / `fromObject()` static factory methods** for deserialization
- **Nullable properties** are omitted from serialized output via `array_filter`
- **String-backed enums** for all domain constants

### HTTP Abstraction

The HTTP layer has three components:

| Class | Responsibility |
|---|---|
| `HttpClient` | Wraps PSR-18 client; creates PSR-7 requests via PSR-17 factories |
| `PaymentsHttpClient` | Adds JWT bearer auth; encodes POST payloads as JWT data tokens |
| `ShippingHttpClient` | Adds JWT bearer auth; sends JSON directly for POST/PATCH; supports query parameters and DELETE |

The Payments API uniquely wraps POST payloads inside a signed JWT (`createDataToken`), while the Shipping API sends standard JSON bodies with bearer token authorization.

### Exception Handling

All exceptions extend `MontonioException` (which extends `RuntimeException`):

| Exception | HTTP Status |
|---|---|
| `AuthenticationException` | 401, 403 |
| `ValidationException` | 400, 422 |
| `NotFoundException` | 404 |
| `ConflictException` | 409 |
| `ApiException` | All other error codes |
| `TransportException` | Network/transport failures |

Each exception carries `statusCode` and `responseBody` properties for debugging.

## Testing

The project uses [PestPHP](https://pestphp.com) v4 with the following test categories:

### Unit Tests

Comprehensive tests for all clients, DTOs, HTTP layer, authentication, webhooks, and enums. Tests use mock PSR-18 clients and JSON fixtures.

### Faker Tests

Property-based style tests using `pestphp/pest-plugin-faker` that verify DTO roundtrip serialization (`toArray` -> `fromArray`) with randomized data for both payment and shipping DTOs.

### Architecture Tests

Pest architecture tests (`tests/Arch/ArchTest.php`) enforce:

- `declare(strict_types=1)` in all source files
- All DTOs are `final readonly`
- All enums are string-backed
- Exception hierarchy is correct
- DTOs do not depend on `Http` or `Auth` layers
- Enums have no dependencies
- Correct naming conventions

### Type Coverage

The `pestphp/pest-plugin-type-coverage` plugin is installed. Run:

```bash
composer test:coverage
```

### Static Analysis

PHPStan is configured at **level max** (the strictest level):

```bash
composer analyse
```

### Running Tests

```bash
# Run all tests
composer test

# Run full QA suite (lint + static analysis + tests)
composer qa

# Check code style
composer lint:check

# Fix code style
composer lint
```

## Security

- **JWT Webhook Verification** — All webhook payloads are verified by decoding signed JWT tokens with HMAC-SHA256 using your secret key. Invalid or expired tokens throw exceptions.
- **Typed DTOs** — All API interactions use strongly-typed DTOs. No raw array passing at public API boundaries.
- **Exception Safety** — All HTTP errors are caught and converted to typed exceptions. PSR-18 transport errors are wrapped in `TransportException`.
- **No Raw Array Returns** — Public client methods always return typed response DTOs.
- **Immutable Objects** — All DTOs and service classes are `final readonly`, preventing mutation after construction.

## Package Structure

```
src/
├── Auth/
│   ├── JwtDecoder.php              # JWT token decoding and verification
│   └── JwtFactory.php              # JWT bearer and data token creation
├── Exception/
│   ├── MontonioException.php       # Base exception (extends RuntimeException)
│   ├── ApiException.php            # Generic API errors
│   ├── AuthenticationException.php # 401/403 errors
│   ├── ConflictException.php       # 409 errors
│   ├── NotFoundException.php       # 404 errors
│   ├── TransportException.php      # Network/transport errors
│   └── ValidationException.php     # 400/422 errors
├── Http/
│   ├── HttpClient.php              # PSR-18 HTTP client wrapper
│   ├── PaymentsHttpClient.php      # Payments-specific HTTP (JWT data tokens)
│   └── ShippingHttpClient.php      # Shipping-specific HTTP (JSON + bearer)
├── Payments/
│   ├── PaymentsClient.php          # Payments API client
│   ├── Dto/
│   │   ├── Request/                # CreateOrderRequest, Payment, LineItem, Address,
│   │   │                           # CreateRefundRequest, CreatePaymentLinkRequest,
│   │   │                           # PaymentInitiationOptions, CardPaymentOptions,
│   │   │                           # BlikOptions, BnplOptions
│   │   └── Response/               # OrderResponse, SessionResponse, RefundResponse,
│   │                               # PaymentLinkResponse, PaymentMethodsResponse,
│   │                               # StoreBalancesResponse, PayoutsResponse,
│   │                               # PayoutExportResponse, and nested DTOs
│   └── Enum/                       # PaymentStatus, RefundStatus, RefundType, Currency,
│                                   # Locale, PaymentMethodCode, PayoutExportType,
│                                   # PayoutSortOrder, PayoutSortBy
├── Shipping/
│   ├── ShippingClient.php          # Shipping API client
│   ├── Dto/
│   │   ├── Request/                # CreateShipmentRequest, UpdateShipmentRequest,
│   │   │                           # CreateLabelFileRequest, CreateWebhookRequest,
│   │   │                           # ShippingRatesRequest, FilterByParcelsRequest,
│   │   │                           # ShipmentShippingMethod, ShipmentSender,
│   │   │                           # ShipmentReceiver, ShipmentParcel, ShipmentProduct,
│   │   │                           # RatesParcel, RatesItem, AdditionalService,
│   │   │                           # CodParams, FilterParcel
│   │   └── Response/               # ShipmentResponse, CarriersResponse,
│   │                               # ShippingMethodsResponse, PickupPointsResponse,
│   │                               # CourierServicesResponse, ShippingRatesResponse,
│   │                               # LabelFileResponse, WebhookResponse,
│   │                               # WebhookListResponse, and nested DTOs
│   └── Enum/                       # ShipmentStatus, LabelFileStatus, ShippingMethodType,
│                                   # PickupPointSubtype, CourierSubtype, LockerSize,
│                                   # ParcelHandoverMethod, PageSize, OrderLabelsBy,
│                                   # DimensionUnit, WeightUnit, ShippingWebhookEvent,
│                                   # AdditionalServiceCode, ContractType
├── Webhook/
│   ├── WebhookVerifier.php         # JWT-based webhook verification
│   └── Dto/
│       ├── PaymentWebhookPayload.php
│       └── ShippingWebhookPayload.php
├── Config.php                      # SDK configuration (readonly)
├── Environment.php                 # Production/Sandbox enum with base URLs
└── Montonio.php                    # Main entry point facade
```

## License

**Montonio SDK** was created by **[Veltix](https://x.com/veltixofficial)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
