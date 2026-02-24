<?php

declare(strict_types=1);

use Veltix\Montonio\Exception\ApiException;
use Veltix\Montonio\Exception\AuthenticationException;
use Veltix\Montonio\Exception\ConflictException;
use Veltix\Montonio\Exception\MontonioException;
use Veltix\Montonio\Exception\NotFoundException;
use Veltix\Montonio\Exception\TransportException;
use Veltix\Montonio\Exception\ValidationException;

test('MontonioException extends RuntimeException', function () {
    $e = new MontonioException;
    expect($e)->toBeInstanceOf(\RuntimeException::class);
});

test('stores statusCode and responseBody', function () {
    $e = new MontonioException(
        message: 'Something went wrong',
        statusCode: 500,
        responseBody: '{"error":"internal"}',
    );

    expect($e->statusCode)->toBe(500)
        ->and($e->responseBody)->toBe('{"error":"internal"}')
        ->and($e->getMessage())->toBe('Something went wrong')
        ->and($e->getCode())->toBe(500);
});

test('stores previous exception', function () {
    $prev = new \RuntimeException('original');
    $e = new MontonioException(message: 'wrapped', previous: $prev);

    expect($e->getPrevious())->toBe($prev);
});

test('defaults are sensible', function () {
    $e = new MontonioException;

    expect($e->statusCode)->toBe(0)
        ->and($e->responseBody)->toBeNull()
        ->and($e->getMessage())->toBe('');
});

dataset('exception_subtypes', [
    'ApiException' => [ApiException::class],
    'AuthenticationException' => [AuthenticationException::class],
    'ConflictException' => [ConflictException::class],
    'NotFoundException' => [NotFoundException::class],
    'TransportException' => [TransportException::class],
    'ValidationException' => [ValidationException::class],
]);

test('subtypes extend MontonioException', function (string $class) {
    $e = new $class(
        message: 'test',
        statusCode: 400,
        responseBody: 'body',
    );

    expect($e)->toBeInstanceOf(MontonioException::class)
        ->and($e)->toBeInstanceOf(\RuntimeException::class)
        ->and($e->statusCode)->toBe(400)
        ->and($e->responseBody)->toBe('body');
})->with('exception_subtypes');
