<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'office_id',
        'working_start_date',
        'working_end_date',
        'working_time_in',
        'working_time_out',
        'working_hours',
    ];

    protected $dates = [
        'working_start_date',
        'working_end_date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function office() {
        return $this->belongsTo(Office::class);
    }
    public function shifts() {
        return $this->hasMany(ScheduleShift::class);
    }
    public function employeeSchedules() {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
