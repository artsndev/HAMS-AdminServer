<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'doctor_id',
        'schedule_id',
        'purpose_of_appointment',
        'session_of_appointment',
        'appointment_time',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     *
     * Define the relationship between User and Appointment models.
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     *
     * Define the relationship between User and Appointment models.
     *
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id');
    }

    /**
     *
     * Define the relationship between User and Appointment models.
     *
     */
    public function schedule()
    {
        return $this->hasMany(Schedule::class,'schedule_id');
    }

    /**
     *
     * Define the relationship between User and Appointment models.
     *
     */
    public function queue()
    {
        return $this->hasOne(Queue::class,'appointment_id');
    }
}
