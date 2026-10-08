<?php

namespace BenefitsMe\ApiAuth\Enums;

enum LoginWith: string
{
    case Email = 'email';

    case Private = 'private';

    case Sso = 'sso';
}
