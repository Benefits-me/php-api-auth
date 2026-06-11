<?php

declare(strict_types=1);

namespace BenefitsMe\ApiAuth\Provider;

use BenefitsMe\ApiAuth\Contracts\AuthServiceInterface;
use BenefitsMe\ApiAuth\Contracts\TokenProviderInterface;
use BenefitsMe\ApiAuth\Exceptions\TokenProviderMissingException;
use BenefitsMe\ApiAuth\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->determineConfigFile(), 'api-auth');

        $this->app->bind(TokenProviderInterface::class, function ($app) {
            $providerClass = config('api-auth.token_provider');

            if ( ! $providerClass) {
                throw new TokenProviderMissingException();
            }

            return $app->make($providerClass);
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(TokenProviderInterface::class)
            );
        });

        $this->app->alias(AuthService::class, AuthServiceInterface::class);
    }

    public function boot(): void
    {
        $this->publishes([
            $this->determineConfigFile() => config_path('api-auth.php'),
        ], 'api-auth-config');
    }

    private function determineConfigFile(): string
    {
        return realpath(__DIR__ . '/../..') . '/config/api-auth.php';
    }

}
