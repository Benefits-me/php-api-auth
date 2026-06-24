<?php

declare(strict_types=1);

namespace BenefitsMe\ApiAuth\Contracts;

interface TokenProviderInterface
{
    public function getToken(): string|null;
}