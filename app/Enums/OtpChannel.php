<?php

namespace App\Enums;

enum OtpChannel: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
    case WHATSAPP = 'whatsapp';
}
