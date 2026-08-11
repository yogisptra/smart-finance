<?php
namespace App\Enums;

enum PaymentMethodType: string
{
    case DEFAULT = 'default';
    case EWALLET = 'ewallet';
    case BANK = 'bank';
    case CASH = 'cash';
}
