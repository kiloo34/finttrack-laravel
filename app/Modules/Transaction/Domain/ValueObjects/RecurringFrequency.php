<?php

namespace App\Modules\Transaction\Domain\ValueObjects;

enum RecurringFrequency: string
{
    case Daily = 'daily';
    case Workdays = 'workdays';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
}
