<?php

namespace App\Enums;

enum OrganizationStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
}
