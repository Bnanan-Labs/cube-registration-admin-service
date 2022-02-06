<?php

namespace App\Enums;

enum FinancialEntryType: string
{
    case payment = 'PAYMENT';
    case purchase = 'PURCHASE';
    case refund = 'REFUND';
    case amendment = 'AMENDMENT';
}
