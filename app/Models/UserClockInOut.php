<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClockInOut extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's conventions
    protected $table = 'user_clock_in_out';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'clock_day',
        'clock_location_status',
        'clock_lat',
        'clock_lon',
        'clock_location_msg_id',
        'clock_distance',
        'clock_time_status',
        'clock_time',
        'work_start_time',
        'clock_selfie_msg_id',
        'clock_status',
        'created_at',
    ];

    // Disable the default timestamps feature (since your table doesn't use created_at and updated_at by default)
    public $timestamps = false;
}
