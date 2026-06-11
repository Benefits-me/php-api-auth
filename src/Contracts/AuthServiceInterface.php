<?php

declare(strict_types=1);

namespace BenefitsMe\ApiAuth\Contracts;

use BenefitsMe\ApiAuth\Enums\LoginWith;
use BenefitsMe\ApiAuth\Exceptions\FailedRequestException;
use Illuminate\Http\Client\ConnectionException;

interface AuthServiceInterface
{
    /**
     * @throws ConnectionException
     * @throws FailedRequestException
     */
    public function login(string $login, string $password): array;

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
        string|null $deviceId = null,
    ): array;

    /**
     * @throws ConnectionException
     */
    public function validateToken(): bool;

    /**
     * @param string $permission The name of the permission.
     * @return bool True if the user has the permission, false otherwise.
     * @throws ConnectionException
     */
    public function hasPermission(string $permission): bool;

    /**
     * @return array{id: int}
     * @throws FailedRequestException
     * @throws ConnectionException
     */
    public function me(): array;
}