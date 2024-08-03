<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'doctor_id',
        'purpose_of_appointment',
        'session_of_appointment',
        'appointment_time',
        'status',
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
}
