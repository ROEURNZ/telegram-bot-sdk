<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserClockIn extends Model
{
    use HasFactory;

    protected $table = 'user_clock_ins';

    protected $fillable = [
        'user_id',
        'clock_in_day',
        'clock_in_location_status',
        'clock_in_lat',
        'clock_in_lon',
        'clock_in_location_msg_id',
        'clock_in_distance',
        'clock_in_time_status',
        'clock_in_time',
        'work_start_time',
        'clock_in_selfie_msg_id',
        'is_clock_in',
    ];

    public $timestamps = true; // Enable timestamps

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWorkDuration()
    {
        return $this->work_start_time ? Carbon::parse($this->work_start_time)->diffInMinutes() : null;
    }
}
