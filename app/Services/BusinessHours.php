<?php

namespace App\Services;

use App\Models\BusinessHoliday;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class BusinessHours
{
    /**
     * @var array<string, true>|null
     */
    private static ?array $holidayCache = null;

    public static function secondsBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        if ($end <= $start) {
            return 0;
        }

        $tz = config('business_hours.timezone');
        $startHour = (int) config('business_hours.start_hour');
        $endHour = (int) config('business_hours.end_hour');
        $workingDays = config('business_hours.working_days');

        $cursor = Carbon::parse($start)->setTimezone($tz);
        $endLocal = Carbon::parse($end)->setTimezone($tz);

        $totalSeconds = 0;
        $maxIterations = 366 * 5;

        while ($cursor < $endLocal && $maxIterations-- > 0) {
            $dayStart = $cursor->copy()->setTime($startHour, 0, 0);
            $dayEnd = $cursor->copy()->setTime($endHour, 0, 0);

            $isWorkingDay = in_array($cursor->dayOfWeek, $workingDays, true)
                && ! self::isHoliday($cursor);

            if ($isWorkingDay) {
                $sliceStart = $cursor->greaterThan($dayStart) ? $cursor : $dayStart;
                $sliceEnd = $endLocal->lessThan($dayEnd) ? $endLocal : $dayEnd;
                if ($sliceEnd > $sliceStart) {
                    $totalSeconds += $sliceEnd->getTimestamp() - $sliceStart->getTimestamp();
                }
            }

            $cursor = $cursor->copy()->addDay()->setTime($startHour, 0, 0);
        }

        return max(0, $totalSeconds);
    }

    public static function hoursBetween(CarbonInterface $start, CarbonInterface $end): float
    {
        return round(self::secondsBetween($start, $end) / 3600, 2);
    }

    private static function isHoliday(CarbonInterface $date): bool
    {
        if (self::$holidayCache === null) {
            try {
                self::$holidayCache = BusinessHoliday::query()
                    ->pluck('date')
                    ->map(fn ($d) => Carbon::parse($d)->toDateString())
                    ->flip()
                    ->all();
            } catch (\Throwable $e) {
                self::$holidayCache = [];
            }
        }

        return isset(self::$holidayCache[$date->toDateString()]);
    }
}
