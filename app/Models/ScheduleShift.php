<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'working_time_in',
        'working_time_out',
        'working_hours',
    ];
}
