<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserClockInOut extends Model
{
    use HasFactory;

    protected $table = 'user_clock_in_out';
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
        'created_at',
    ];

    public $timestamps = false;

    // UserClockInOut belongs to User
    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }
}
