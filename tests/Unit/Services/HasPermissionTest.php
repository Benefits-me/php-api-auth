<?php

declare(strict_types=1);

use BenefitsMe\ApiAuth\Services\AuthService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('api-auth.url', 'https://fake-auth-api.com');
    Config::set('api-auth.version', 'v1');
    $this->authService = new AuthService;
});

test('it returns true when user has permission', function () {
    $apiUrl = config('api-auth.url');
    Http::fake([
        $apiUrl . '/*' => Http::response(null, 200),
    ]);
    $permission = 'view-reports';
    $token = 'valid-user-token';

    $result = $this->authService->hasPermission($permission, $token);

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

    $result = $this->authService->hasPermission($permission, $token);

    expect($result)->toBeFalse();
});

test('it throws a connection exception on network failure', function () {
    Http::fake(fn () => throw new ConnectionException('Network error'));

    expect(fn () => $this->authService->hasPermission('any-permission', 'any-token'))
        ->toThrow(ConnectionException::class);
});
