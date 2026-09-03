<?php

namespace App\Modules\Transaction\Infrastructure\Services;

use App\Modules\Transaction\Domain\Services\HolidayServiceInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class IndonesianHolidayService implements HolidayServiceInterface
{
    public function isWorkday(CarbonImmutable $date): bool
    {
        if ($date->isWeekend()) {
            return false;
        }

        return ! $this->isHoliday($date);
    }

    public function isHoliday(CarbonImmutable $date): bool
    {
        $year = $date->year;
        $dateString = $date->format('Y-m-d');
        $holidays = $this->getHolidaysForYear($year);

        return in_array($dateString, $holidays, true);
    }

    /**
     * @return string[] Array of holiday dates in Y-m-d format
     */
    private function getHolidaysForYear(int $year): array
    {
        // Cache the holidays for this year for 30 days
        return Cache::remember("holidays_indonesia_{$year}", now()->addDays(30), function () use ($year) {
            try {
                // Using a free API for Indonesian public holidays
                $response = Http::timeout(5)->get("https://dayoffapi.vercel.app/api?year={$year}");

                if ($response->successful()) {
                    $data = $response->json();
                    $holidayDates = [];

                    foreach ($data as $holiday) {
                        // Normally this API returns {tanggal: "2024-01-01", ...} or similar
                        // Fallback handling if response changes
                        if (isset($holiday['tanggal'])) {
                            $holidayDates[] = $holiday['tanggal'];
                        }
                    }

                    return $holidayDates;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch Indonesian holidays for year {$year}: ".$e->getMessage());
            }

            // Fallback: If API fails, return empty array (only weekends will be skipped)
            return [];
        });
    }
}
