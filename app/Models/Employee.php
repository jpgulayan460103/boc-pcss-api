<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'position',
        'is_overtimer',
        'office_id',
        'position_id',
    ];


    public static function boot()
    {
        parent::boot();
        self::saved(function ($model) {
            $model->full_name = $model->last_name.", ".$model->first_name." ".$model->middle_name;
        });
    }

    public function office() : BelongsTo {
        return $this->belongsTo(Office::class);
    }

    public function position() : BelongsTo {
        return $this->belongsTo(Position::class);
    }
}
