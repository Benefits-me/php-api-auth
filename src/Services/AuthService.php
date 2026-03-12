<?php

declare(strict_types=1);

namespace BenefitsMe\ApiAuth\Services;

use BenefitsMe\ApiAuth\Enums\LoginWith;
use BenefitsMe\ApiAuth\Exceptions\FailedRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AuthService
{
    protected string $apiBaseUrl;

    protected string $apiVersion;

    public function __construct()
    {
        $this->apiBaseUrl = config('api-auth.url');
        $this->apiVersion = config('api-auth.version');
    }

    private function buildHeader(array $headers = []): array
    {
        return array_merge([
            'Accept' => 'application/json',
            'X-API-Version' => $this->apiVersion,
        ], $headers);
    }

    private function httpClient(
        string|null $token = null,
        array $headers = [],
    ): PendingRequest
    {
        $request = Http::withHeaders($this->buildHeader($headers));

        if (null !== $token) {
            $request->withToken($token);
        }

        return $request;
    }

    private function url(string $path): string
    {
        return $this->apiBaseUrl . $path;
    }

    /**
     * @throws ConnectionException
     * @throws FailedRequestException
     */
    public function login(string $login, string $password): array
    {
        $response = $this->httpClient()
            ->post($this->url('/login'), [
                'login' => $login,
                'password' => $password,
            ]);

        if ( ! $response->successful()) {
            throw new FailedRequestException('Login failed!');
        }

        return $response->json();
    }

    /**
     * @throws FailedRequestException
     * @throws ConnectionException
     */
    public function register(
        string $firstName,
        string $lastName,
        LoginWith $loginWith,
        string $login,
        string $password,
        int $companyId,
        int $originId,
        int $regionId,
        int $registrationPlatformId,
        int $roleId,
        string|null $deviceId,
    ): array
    {
        $response = $this->httpClient()
            ->post($this->url('/register'), [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'login_with' => $loginWith->value,
                'login_email' => $login,
                'password' => $password,
                'company_id' => $companyId,
                'origin_id' => $originId,
                'region_id' => $regionId,
                'registration_platform_id' => $registrationPlatformId,
                'role_id' => $roleId,
                'device_id' => $deviceId,
            ]);

        if ( ! $response->successful()) {
            throw new FailedRequestException('Registration failed!');
        }

        return $response->json();
    }

    /**
     * @throws ConnectionException
     */
    public function validateToken(string $token): bool
    {
        $response = $this->httpClient($token)
            ->get($this->url('/validate-token'));

        return $response->successful();
    }
}
