<?php

declare(strict_types=1);

namespace Veltix\Montonio\Support;

use InvalidArgumentException;

/**
 * Decimal amounts and measures as strings ("12.30", "0.5", "100"), never as
 * floats: money keeps every cent it had on the wire.
 *
 * Montonio's JSON writes them as numbers. Responses are decoded with
 * Json::decode(), which keeps a fractional number's digits as a string, so
 * fromValue() never meets a float there. Requests write a decimal back as a
 * JSON number (toNumber()): an integer as it is, anything else through a
 * float only at the moment it is encoded, whose shortest representation
 * (PHP's default serialize_precision) prints the same digits.
 *
 * @internal
 */
final class Decimal
{
    private const PATTERN = '/^-?(0|[1-9]\d*)(\.\d+)?$/D';

    /** The decimal $value as a string; refuses anything that is not a plain decimal. */
    public static function of(string $value): string
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s" is not a decimal: use digits with an optional leading minus and an optional point.', $value));
        }

        return $value;
    }

    /** A decoded JSON value (an integer, a decimal string or a numeric string) as a decimal string. */
    public static function fromValue(mixed $value): string
    {
        return match (true) {
            is_int($value) => (string) $value,
            is_string($value) && is_numeric($value) => self::normalize($value),
            is_float($value) => self::normalize(json_encode($value, JSON_THROW_ON_ERROR)),
            default => throw new InvalidArgumentException('Expected a number, got '.get_debug_type($value).'.'),
        };
    }

    /** The decimal string $value as the JSON number Montonio expects. */
    public static function toNumber(string $value): int|float
    {
        self::of($value);

        if (! str_contains($value, '.')) {
            return (int) $value;
        }

        return (float) $value;
    }

    private static function normalize(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^(-?)(\d+)(?:\.(\d+))?[eE]([+-]?\d+)$/D', $value, $parts) === 1) {
            return self::expand($parts[1], $parts[2], $parts[3], (int) $parts[4]);
        }

        if (str_starts_with($value, '+')) {
            $value = substr($value, 1);
        }

        if (str_starts_with($value, '.')) {
            $value = '0'.$value;
        }

        return self::of($value);
    }

    /** A number in scientific notation written out: ("1", "5", -3) is "0.0015". */
    private static function expand(string $sign, string $whole, string $fraction, int $exponent): string
    {
        $digits = ltrim($whole.$fraction, '0');
        $point = strlen(ltrim($whole, '0')) + $exponent;

        if ($digits === '') {
            return '0';
        }

        if ($point <= 0) {
            $result = '0.'.str_repeat('0', -$point).$digits;
        } elseif ($point >= strlen($digits)) {
            $result = $digits.str_repeat('0', $point - strlen($digits));
        } else {
            $result = substr($digits, 0, $point).'.'.substr($digits, $point);
        }

        if (str_contains($result, '.')) {
            $result = rtrim(rtrim($result, '0'), '.');
        }

        return $sign.$result;
    }
}
