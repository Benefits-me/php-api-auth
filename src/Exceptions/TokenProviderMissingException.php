<?php

declare(strict_types=1);

namespace BenefitsMe\ApiAuth\Exceptions;

use Exception;
use Throwable;

class TokenProviderMissingException extends Exception
{
    public function __construct(
        string $message = "Token provider is not configured in api-auth.php. Please set 'token_provider'.",
        int $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}