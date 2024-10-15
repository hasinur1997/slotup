<?php

namespace App\Booking;

use App\Models\Employee;
use App\Models\ScheduleExclusion;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

class ScheduleAvailability
{
    protected PeriodCollection $periods;

    public function __construct(protected Employee $employee, protected Service $service)
    {
        $this->periods = new PeriodCollection();
    }

    public function forPeriod(Carbon $startsAt, Carbon $endsAt)
    {
        collect(CarbonPeriod::create($startsAt, $endsAt)->days())

        ->each(function(Carbon $date) {
            $this->availabilityFromSchedule($date);

            $this->employee->scheduleExclutions->each(function(ScheduleExclusion $scheduleExclusion) {
                $this->subtractScheduleExclusions($scheduleExclusion);
            });
        });


        return $this->periods;
    }

    protected function availabilityFromSchedule(Carbon $date)
    {
        if( ! $schedule = $this->employee->schedules->where('start_at', '<=', $date)->where('end_at', '>=', $date)->first() ) {
            return;
        }

        if (![$startAt, $endAt] = $schedule->getWorkingHoursForDate($date)) {
            return;
        }

        $this->periods = $this->periods->add(
            Period::make(
                $date->copy()->setTimeFromTimeString($startAt),
                $date->copy()->setTimeFromTimeString($endAt)->subMinutes($this->service->duration),
                Precision::MINUTE()
            )
        );
    }

    protected function subtractScheduleExclusions(ScheduleExclusion $scheduleExclusion) {
        $this->periods = $this->periods->subtract(
            Period::make(
                $scheduleExclusion->start_at,
                $scheduleExclusion->end_at,
                Precision::MINUTE()
            )
        );
    }
}
