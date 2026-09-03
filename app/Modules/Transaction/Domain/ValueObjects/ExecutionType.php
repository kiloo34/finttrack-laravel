<?php

namespace App\Modules\Transaction\Domain\ValueObjects;

enum ExecutionType: string
{
    case AutoComplete = 'auto_complete';
    case Draft = 'draft';
}
