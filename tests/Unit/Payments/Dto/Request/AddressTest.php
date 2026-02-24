<?php

declare(strict_types=1);

use Veltix\Montonio\Payments\Dto\Request\Address;

test('toArray filters null values', function () {
    $address = new Address(firstName: 'John', lastName: 'Doe');
    $array = $address->toArray();

    expect($array)->toBe(['firstName' => 'John', 'lastName' => 'Doe'])
        ->and($array)->not->toHaveKey('email')
        ->and($array)->not->toHaveKey('phoneNumber');
});

test('toArray includes all non-null values', function () {
    $address = new Address(
        firstName: 'John',
        lastName: 'Doe',
        email: 'john@example.com',
        phoneNumber: '+3721234567',
        phoneCountry: 'EE',
        addressLine1: '123 Main St',
        addressLine2: 'Apt 4',
        locality: 'Tallinn',
        region: 'Harjumaa',
        country: 'EE',
        postalCode: '10115',
        companyName: 'Acme',
        companyLegalName: 'Acme OÜ',
        companyRegCode: '12345678',
        companyVatNumber: 'EE123456789',
    );

    $array = $address->toArray();
    expect($array)->toHaveCount(15)
        ->and($array['firstName'])->toBe('John')
        ->and($array['companyVatNumber'])->toBe('EE123456789');
});

test('fromArray creates Address from array', function () {
    $data = ['firstName' => 'Jane', 'email' => 'jane@example.com', 'country' => 'LT'];
    $address = Address::fromArray($data);

    expect($address->firstName)->toBe('Jane')
        ->and($address->email)->toBe('jane@example.com')
        ->and($address->country)->toBe('LT')
        ->and($address->lastName)->toBeNull();
});

test('roundtrip toArray/fromArray', function () {
    $original = new Address(firstName: 'John', lastName: 'Doe', email: 'john@example.com');
    $restored = Address::fromArray($original->toArray());

    expect($restored->firstName)->toBe($original->firstName)
        ->and($restored->lastName)->toBe($original->lastName)
        ->and($restored->email)->toBe($original->email)
        ->and($restored->phoneNumber)->toBeNull();
});
