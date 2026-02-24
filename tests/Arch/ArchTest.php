<?php

declare(strict_types=1);

// ────────────────────────────────────────────────────
// Section 1 — Strict Types
// ────────────────────────────────────────────────────

arch('all source files use strict types')
    ->expect('Veltix\Montonio')
    ->toUseStrictTypes();

// ────────────────────────────────────────────────────
// Section 2 — Immutability
// ────────────────────────────────────────────────────

arch('payment request DTOs are final and readonly')
    ->expect('Veltix\Montonio\Payments\Dto\Request')
    ->toBeFinal()
    ->toBeReadonly();

arch('payment response DTOs are final and readonly')
    ->expect('Veltix\Montonio\Payments\Dto\Response')
    ->toBeFinal()
    ->toBeReadonly();

arch('shipping request DTOs are final and readonly')
    ->expect('Veltix\Montonio\Shipping\Dto\Request')
    ->toBeFinal()
    ->toBeReadonly();

arch('shipping response DTOs are final and readonly')
    ->expect('Veltix\Montonio\Shipping\Dto\Response')
    ->toBeFinal()
    ->toBeReadonly();

arch('webhook DTOs are final and readonly')
    ->expect('Veltix\Montonio\Webhook\Dto')
    ->toBeFinal()
    ->toBeReadonly();

arch('auth classes are final and readonly')
    ->expect('Veltix\Montonio\Auth')
    ->toBeFinal()
    ->toBeReadonly();

arch('http classes are final and readonly')
    ->expect('Veltix\Montonio\Http')
    ->toBeFinal()
    ->toBeReadonly();

arch('config is final and readonly')
    ->expect('Veltix\Montonio\Config')
    ->toBeFinal()
    ->toBeReadonly();

arch('PaymentsClient is final and readonly')
    ->expect('Veltix\Montonio\Payments\PaymentsClient')
    ->toBeFinal()
    ->toBeReadonly();

arch('ShippingClient is final and readonly')
    ->expect('Veltix\Montonio\Shipping\ShippingClient')
    ->toBeFinal()
    ->toBeReadonly();

arch('WebhookVerifier is final and readonly')
    ->expect('Veltix\Montonio\Webhook\WebhookVerifier')
    ->toBeFinal()
    ->toBeReadonly();

// ────────────────────────────────────────────────────
// Section 3 — Montonio Facade
// ────────────────────────────────────────────────────

arch('Montonio facade is final')
    ->expect('Veltix\Montonio\Montonio')
    ->toBeFinal();

arch('Montonio facade is not readonly')
    ->expect('Veltix\Montonio\Montonio')
    ->not->toBeReadonly();

// ────────────────────────────────────────────────────
// Section 4 — Exception Hierarchy
// ────────────────────────────────────────────────────

arch('MontonioException extends RuntimeException')
    ->expect('Veltix\Montonio\Exception\MontonioException')
    ->toExtend('RuntimeException');

arch('all exceptions extend MontonioException')
    ->expect('Veltix\Montonio\Exception')
    ->toExtend('Veltix\Montonio\Exception\MontonioException')
    ->ignoring('Veltix\Montonio\Exception\MontonioException');

arch('all exception classes have Exception suffix')
    ->expect('Veltix\Montonio\Exception')
    ->toHaveSuffix('Exception');

// ────────────────────────────────────────────────────
// Section 5 — Enums
// ────────────────────────────────────────────────────

arch('payment enums are string-backed')
    ->expect('Veltix\Montonio\Payments\Enum')
    ->toBeStringBackedEnums();

arch('shipping enums are string-backed')
    ->expect('Veltix\Montonio\Shipping\Enum')
    ->toBeStringBackedEnums();

arch('Environment is a string-backed enum')
    ->expect('Veltix\Montonio\Environment')
    ->toBeStringBackedEnums();

// ────────────────────────────────────────────────────
// Section 6 — Dependency Rules
// ────────────────────────────────────────────────────

arch('payment DTOs do not depend on Http')
    ->expect('Veltix\Montonio\Payments\Dto')
    ->not->toUse('Veltix\Montonio\Http');

arch('shipping DTOs do not depend on Http')
    ->expect('Veltix\Montonio\Shipping\Dto')
    ->not->toUse('Veltix\Montonio\Http');

arch('webhook DTOs do not depend on Http')
    ->expect('Veltix\Montonio\Webhook\Dto')
    ->not->toUse('Veltix\Montonio\Http');

arch('payment DTOs do not depend on Auth')
    ->expect('Veltix\Montonio\Payments\Dto')
    ->not->toUse('Veltix\Montonio\Auth');

arch('shipping DTOs do not depend on Auth')
    ->expect('Veltix\Montonio\Shipping\Dto')
    ->not->toUse('Veltix\Montonio\Auth');

arch('webhook DTOs do not depend on Auth')
    ->expect('Veltix\Montonio\Webhook\Dto')
    ->not->toUse('Veltix\Montonio\Auth');

arch('payment enums use nothing')
    ->expect('Veltix\Montonio\Payments\Enum')
    ->toUseNothing();

arch('shipping enums use nothing')
    ->expect('Veltix\Montonio\Shipping\Enum')
    ->toUseNothing();

arch('exceptions do not depend on Http')
    ->expect('Veltix\Montonio\Exception')
    ->not->toUse('Veltix\Montonio\Http');

arch('exceptions do not depend on Auth')
    ->expect('Veltix\Montonio\Exception')
    ->not->toUse('Veltix\Montonio\Auth');

// ────────────────────────────────────────────────────
// Section 7 — No Debugging Code
// ────────────────────────────────────────────────────

arch('no debugging functions are used')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'die', 'exit', 'ray'])
    ->not->toBeUsed();

// ────────────────────────────────────────────────────
// Section 8 — Naming Conventions
// ────────────────────────────────────────────────────

arch('PaymentsClient has Client suffix')
    ->expect('Veltix\Montonio\Payments\PaymentsClient')
    ->toHaveSuffix('Client');

arch('ShippingClient has Client suffix')
    ->expect('Veltix\Montonio\Shipping\ShippingClient')
    ->toHaveSuffix('Client');
