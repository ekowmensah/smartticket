<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case PROSPECT = 'prospect';
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
}
