<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'email_verified_at',
        'password',
        'role',
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'image_path',
        'office_id',
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


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function office() : BelongsTo {
        return $this->belongsTo(Office::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
