<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserClockOut extends Model
{
    use HasFactory;

    protected $table = 'user_clock_outs';

    protected $fillable = [
        'user_id',
        'clock_out_day',
        'clock_out_location_status',
        'clock_out_lat',
        'clock_out_lon',
        'clock_out_location_msg_id',
        'clock_out_distance',
        'clock_out_time_status',
        'clock_out_time',
        'work_end_time',
        'clock_out_selfie_msg_id',
        'is_clock_out',
    ];

    public $timestamps = true; // Enable timestamps

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWorkDuration()
    {
        return $this->work_end_time ? Carbon::parse($this->work_end_time)->diffInMinutes() : null;
    }
}
