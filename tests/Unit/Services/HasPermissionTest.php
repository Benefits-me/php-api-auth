<?php

declare(strict_types=1);

use BenefitsMe\ApiAuth\Contracts\TokenProviderInterface;
use BenefitsMe\ApiAuth\Services\AuthService;
use BenefitsMe\ApiAuth\Exceptions\FailedRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('api-auth.url', 'https://fake-auth-api.com');
    Config::set('api-auth.version', 'v1');

    $this->tokenProviderMock = Mockery::mock(TokenProviderInterface::class);
    $this->authService = new AuthService($this->tokenProviderMock);
});

test('it returns true when user has permission', function () {
    $apiUrl = config('api-auth.url');
    Http::fake([
        $apiUrl . '/*' => Http::response(null, 200),
    ]);

    $permission = 'view-reports';
    $token = 'valid-user-token';

    $this->tokenProviderMock->shouldReceive('getToken')->once()->andReturn($token);

    $result = $this->authService->hasPermission($permission);

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) use ($permission, $token, $apiUrl) {
        return $request->hasHeader('Authorization', 'Bearer ' . $token)
            && $request->hasHeader('X-API-Version', 'v1')
            && $request->url() === "{$apiUrl}/permissions/has/{$permission}";
    });
});

test('it returns false when user does not have permission', function () {
    Http::fake([
        config('api-auth.url') . '/*' => Http::response(['message' => 'Forbidden'], 403),
    ]);

    $permission = 'delete-everything';
    $token = 'user-with-no-permission';

    $this->tokenProviderMock->shouldReceive('getToken')->once()->andReturn($token);

    $result = $this->authService->hasPermission($permission);

    expect($result)->toBeFalse();
});

test('it throws a connection exception on network failure', function () {
    Http::fake(fn () => throw new ConnectionException('Network error'));

    $this->tokenProviderMock->shouldReceive('getToken')->once()->andReturn('any-token');

    expect(fn () => $this->authService->hasPermission('any-permission'))
        ->toThrow(ConnectionException::class);
});

test('it throws a failed request exception on 500 server error when checking permission', function () {
    Http::fake([
        config('api-auth.url') . '/*' => Http::response([], 500),
    ]);

    $this->tokenProviderMock->shouldReceive('getToken')->once()->andReturn('any-token');

    expect(fn () => $this->authService->hasPermission('view-reports'))
        ->toThrow(FailedRequestException::class, 'Permission check failed due to a server error.');
});

test('it throws a failed request exception on 500 server error when validating token', function () {
    Http::fake([
        config('api-auth.url') . '/*' => Http::response([], 500),
    ]);

    $this->tokenProviderMock->shouldReceive('getToken')->once()->andReturn('any-token');

    expect(fn () => $this->authService->validateToken())
        ->toThrow(FailedRequestException::class, 'Token validation failed due to a server error.');
});

