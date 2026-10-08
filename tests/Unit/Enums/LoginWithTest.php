<?php

declare(strict_types=1);

use BenefitsMe\ApiAuth\Enums\LoginWith;

test('login with knows email, private and sso', function () {
    expect(array_map(fn (LoginWith $case): string => $case->value, LoginWith::cases()))
        ->toBe(['email', 'private', 'sso']);
});
