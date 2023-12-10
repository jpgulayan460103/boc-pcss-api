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
        'working_date',
        'working_time_in',
        'working_time_out',
        'working_hours',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function office() {
        return $this->belongsTo(Office::class);
    }
}
