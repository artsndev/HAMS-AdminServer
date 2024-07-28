<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
    ];

    /**
     *
     * Define the relationship between Doctor and Schedule models.
     *
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
