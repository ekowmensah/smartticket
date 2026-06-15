<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INVITED = 'invited';
    case SUSPENDED = 'suspended';
}
