<?php

declare(strict_types=1);

use Veltix\Montonio\Shipping\Dto\Response\Carrier;
use Veltix\Montonio\Shipping\Dto\Response\CarrierContract;
use Veltix\Montonio\Shipping\Dto\Response\CarriersResponse;
use Veltix\Montonio\Shipping\Dto\Response\ContractCredentials;

test('fromArray maps carriers array', function () {
    $response = CarriersResponse::fromArray([
        'carriers' => [
            [
                'id' => 'carrier-1',
                'code' => 'omniva',
                'name' => 'Omniva',
                'logoUrl' => 'https://example.com/omniva.png',
                'contracts' => [],
                'hasMontonioContract' => true,
                'supportedContractTypes' => ['DIRECT', 'MONTONIO'],
            ],
        ],
    ]);

    expect($response->carriers)->toHaveCount(1)
        ->and($response->carriers[0])->toBeInstanceOf(Carrier::class);
});

test('Carrier maps all fields', function () {
    $carrier = Carrier::fromArray([
        'id' => 'c-1',
        'code' => 'dpd',
        'name' => 'DPD',
        'logoUrl' => 'https://example.com/dpd.png',
        'contracts' => null,
        'hasMontonioContract' => false,
        'supportedContractTypes' => ['DIRECT'],
    ]);

    expect($carrier->id)->toBe('c-1')
        ->and($carrier->code)->toBe('dpd')
        ->and($carrier->name)->toBe('DPD')
        ->and($carrier->logoUrl)->toBe('https://example.com/dpd.png')
        ->and($carrier->contracts)->toBeNull()
        ->and($carrier->hasMontonioContract)->toBeFalse()
        ->and($carrier->supportedContractTypes)->toBe(['DIRECT']);
});

test('Carrier with null contracts vs non-null contracts', function () {
    $withContracts = Carrier::fromArray([
        'id' => 'c-1',
        'code' => 'omniva',
        'name' => 'Omniva',
        'logoUrl' => 'https://example.com/logo.png',
        'contracts' => [
            [
                'id' => 'contract-1',
                'carrierId' => 'c-1',
                'country' => 'EE',
                'lastUsedParcelNumber' => 'P001',
                'daysAllowedForReturns' => 14,
                'isDirectContract' => true,
                'createdAt' => '2025-01-01T00:00:00Z',
                'returnsAllowed' => true,
                'parcelHandoverMethod' => 'courierPickUp',
                'defaultLockerSize' => 'M',
                'logisticsContractNumber' => 'LC-001',
                'credentials' => ['username' => 'user1'],
            ],
        ],
        'hasMontonioContract' => true,
        'supportedContractTypes' => [],
    ]);

    expect($withContracts->contracts)->toHaveCount(1)
        ->and($withContracts->contracts[0])->toBeInstanceOf(CarrierContract::class);

    $withoutContracts = Carrier::fromArray([
        'id' => 'c-2',
        'code' => 'dpd',
        'name' => 'DPD',
        'logoUrl' => 'https://example.com/dpd.png',
    ]);

    expect($withoutContracts->contracts)->toBeNull();
});

test('CarrierContract and ContractCredentials', function () {
    $contract = CarrierContract::fromArray([
        'id' => 'contract-1',
        'carrierId' => 'carrier-1',
        'country' => 'EE',
        'lastUsedParcelNumber' => null,
        'daysAllowedForReturns' => 30,
        'isDirectContract' => false,
        'createdAt' => '2025-01-15T10:00:00Z',
        'returnsAllowed' => true,
        'parcelHandoverMethod' => 'terminalDropOff',
        'defaultLockerSize' => null,
        'logisticsContractNumber' => null,
        'credentials' => ['username' => 'testuser'],
    ]);

    expect($contract->id)->toBe('contract-1')
        ->and($contract->carrierId)->toBe('carrier-1')
        ->and($contract->country)->toBe('EE')
        ->and($contract->lastUsedParcelNumber)->toBeNull()
        ->and($contract->daysAllowedForReturns)->toBe(30)
        ->and($contract->isDirectContract)->toBeFalse()
        ->and($contract->returnsAllowed)->toBeTrue()
        ->and($contract->parcelHandoverMethod)->toBe('terminalDropOff')
        ->and($contract->defaultLockerSize)->toBeNull()
        ->and($contract->credentials)->toBeInstanceOf(ContractCredentials::class)
        ->and($contract->credentials->username)->toBe('testuser');
});

test('ContractCredentials defaults', function () {
    $creds = ContractCredentials::fromArray([]);

    expect($creds->username)->toBeNull();
});

test('fromArray with docs fixture', function () {
    $data = \Veltix\Montonio\Tests\fixture('Shipping/carriers.json');
    $response = CarriersResponse::fromArray($data);

    expect($response->carriers)->toHaveCount(4);

    $smartpost = $response->carriers[0];
    expect($smartpost->code)->toBe('smartpost')
        ->and($smartpost->name)->toBe('SmartPosti')
        ->and($smartpost->contracts)->toBeNull()
        ->and($smartpost->hasMontonioContract)->toBeTrue()
        ->and($smartpost->supportedContractTypes)->toBe(['DIRECT', 'MONTONIO']);

    $omniva = $response->carriers[1];
    expect($omniva->code)->toBe('omniva')
        ->and($omniva->contracts)->toHaveCount(1)
        ->and($omniva->contracts[0]->country)->toBe('EE')
        ->and($omniva->contracts[0]->isDirectContract)->toBeTrue()
        ->and($omniva->contracts[0]->credentials->username)->toBe('my_username');

    $venipak = $response->carriers[3];
    expect($venipak->code)->toBe('venipak')
        ->and($venipak->hasMontonioContract)->toBeFalse()
        ->and($venipak->supportedContractTypes)->toBe(['DIRECT'])
        ->and($venipak->contracts[0]->lastUsedParcelNumber)->toBe('12350');
});
