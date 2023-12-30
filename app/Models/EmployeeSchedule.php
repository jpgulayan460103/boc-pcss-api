<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'schedule_id',
        'schedule_shift_id',
        'working_date',
        'is_overtime',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }

    public function schedule_shift() {
        return $this->belongsTo(ScheduleShift::class);
    }
}
