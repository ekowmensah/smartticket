<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case LOGIN = 'login';
    case REGISTRATION = 'registration';
    case TEAM_INVITATION = 'team_invitation';
    case TICKET_CLAIM = 'ticket_claim';
}
