<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $casts = [
        'start_at' => 'date',
        'end_at'   => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkingHoursForDate(Carbon $date)
    {
        $hours = array_filter([
            $this->{strtolower($date->format('l')) . '_start_at'},
            $this->{strtolower($date->format('l')) . '_end_at'},
        ]);

        return !empty($hours) ? $hours : null;
    }
}
