<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case missingPayment = 'MISSING_PAYMENT';
    case partiallyPaid = 'PARTIAL_PAYMENT';
    case paid = 'FULL_PAYMENT';
    case needsPartialRefund = 'NEEDS_PARTIAL_REFUND';
    case refunded = 'REFUNDED';
}
