<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum PaymentProvider: string
{
    case Stripe = 'Stripe';
    case Paypal = 'Paypal';
    case BankTransfer = 'BankTransfer';
}
