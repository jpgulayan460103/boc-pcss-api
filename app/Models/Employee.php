<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'is_overtimer',
        'office_id',
        'position_id',
    ];


    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->full_name = trim($model->last_name.", ".$model->first_name." ".$model->middle_name);
        });
        self::updating(function ($model) {
            $model->full_name = trim($model->last_name.", ".$model->first_name." ".$model->middle_name);
        });
    }

    public function office() : BelongsTo {
        return $this->belongsTo(Office::class);
    }

    public function position() : BelongsTo {
        return $this->belongsTo(Position::class);
    }

    public function employeeSchedules() : HasMany {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
