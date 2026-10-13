<?php

declare(strict_types=1);

use Veltix\Montonio\Support\Decimal;
use Veltix\Montonio\Support\Json;

test('Json::decode keeps a fractional number as the string Montonio wrote', function () {
    $decoded = Json::decode('{"grandTotal": 99.90, "count": 3, "rate": 1e-2, "name": "1.50 \"x\" 2.5", "list": [0.1, -0.30, 12]}');

    expect($decoded['grandTotal'])->toBe('99.90')
        ->and($decoded['count'])->toBe(3)
        ->and($decoded['rate'])->toBe('1e-2')
        ->and($decoded['name'])->toBe('1.50 "x" 2.5')
        ->and($decoded['list'])->toBe(['0.1', '-0.30', 12]);
});

test('Json::decode refuses what is not an object or an array', function () {
    Json::decode('12.5');
})->throws(JsonException::class);

test('Decimal::fromValue writes every number as a plain decimal string', function (mixed $value, string $expected) {
    expect(Decimal::fromValue($value))->toBe($expected);
})->with([
    'an integer' => [25, '25'],
    'a decimal string' => ['19.90', '19.90'],
    'an exponent' => ['1e-2', '0.01'],
    'a large exponent' => ['1.5E3', '1500'],
    'a negative' => ['-0.05', '-0.05'],
    'a float' => [0.1, '0.1'],
]);

test('Decimal::fromValue refuses what is no number', function (mixed $value) {
    Decimal::fromValue($value);
})->with([['abc'], [null], [[1]]])->throws(InvalidArgumentException::class);

test('Decimal::toNumber writes a decimal as the JSON number it is', function () {
    expect(json_encode([Decimal::toNumber('19.90'), Decimal::toNumber('100'), Decimal::toNumber('0.07'), Decimal::toNumber('1234567.89')]))
        ->toBe('[19.9,100,0.07,1234567.89]');
});

test('Decimal::toNumber refuses what is no plain decimal', function (string $value) {
    Decimal::toNumber($value);
})->with([['12,50'], ['1e2'], [''], ['.5']])->throws(InvalidArgumentException::class);
