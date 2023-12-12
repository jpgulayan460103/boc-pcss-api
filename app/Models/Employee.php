<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'position',
    ];


    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->full_name = $model->first_name." ".$model->middle_name." ".$model->last_name;
        });
        self::updating(function ($model) {
            $model->full_name = $model->first_name." ".$model->middle_name." ".$model->last_name;
        });
    }
}
