<?php

declare(strict_types=1);

namespace Veltix\Montonio\Support;

use JsonException;

/**
 * JSON decoding that never makes a float: a number with a fraction or an
 * exponent ("12.30", "1e2") is kept as the string of its digits, so an
 * amount reaches its DTO exactly as Montonio wrote it. Integers stay
 * integers (beyond PHP_INT_MAX they become strings too).
 *
 * @internal
 */
final class Json
{
    /**
     * @return array<array-key, mixed>
     *
     * @throws JsonException when $json is not a JSON object or array
     */
    public static function decode(string $json): array
    {
        $decoded = json_decode(self::quoteDecimals($json), true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);

        if (! is_array($decoded)) {
            throw new JsonException('Expected a JSON object or array.');
        }

        return $decoded;
    }

    /** $json with every number that has a fraction or an exponent written as a string. */
    private static function quoteDecimals(string $json): string
    {
        $out = '';
        $length = strlen($json);
        $i = 0;

        while ($i < $length) {
            $char = $json[$i];

            if ($char === '"') {
                $end = $i + 1;

                while ($end < $length && $json[$end] !== '"') {
                    $end += $json[$end] === '\\' ? 2 : 1;
                }

                $out .= substr($json, $i, $end - $i + 1);
                $i = $end + 1;

                continue;
            }

            if ($char === '-' || ctype_digit($char)) {
                preg_match('/-?\d+(\.\d+)?([eE][+-]?\d+)?/A', $json, $match, 0, $i);
                $number = $match[0] ?? $char;
                $isDecimal = ($match[1] ?? '') !== '' || ($match[2] ?? '') !== '';
                $out .= $isDecimal ? '"'.$number.'"' : $number;
                $i += strlen($number);

                continue;
            }

            $out .= $char;
            $i++;
        }

        return $out;
    }
}
