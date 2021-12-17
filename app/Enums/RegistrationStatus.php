<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case pending = 'PENDING';
    case waitingList = 'WAITING_LIST';
    case declined = 'DECLINED';
    case approved = 'APPROVED';
}
