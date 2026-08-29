<?php

namespace App\Modules\Transaction\Domain\ValueObjects;

enum TransactionStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
