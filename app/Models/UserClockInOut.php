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

    // Disable the default timestamps feature (since your table doesn't use created_at and updated_at by default)
    public $timestamps = false;
}
