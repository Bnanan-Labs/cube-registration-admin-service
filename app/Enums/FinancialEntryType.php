<?php

namespace App\Enums;

enum FinancialEntryType: string
{
    case baseFee = 'BASE_FEE';
    case eventFee = 'EVENT_FEE';
    case guestFee = 'GUEST_FEE';
    case refund = 'REFUND';
    case discount = 'DISCOUNT';
    case payment = 'PAYMENT';
}
