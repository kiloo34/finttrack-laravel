<?php

namespace App\Modules\Transaction\Domain\Services;

use Carbon\CarbonImmutable;

interface HolidayServiceInterface
{
    /**
     * Checks if a given date is a workday (not a weekend and not a public holiday).
     */
    public function isWorkday(CarbonImmutable $date): bool;

    /**
     * Checks if a given date is a public holiday.
     */
    public function isHoliday(CarbonImmutable $date): bool;
}
