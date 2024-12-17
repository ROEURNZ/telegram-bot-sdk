<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWorkingHour extends Model
{
    use HasFactory;

    protected $table = 'user_working_hour';
    protected $fillable = [
        'user_id',
        'work_day',
        'start_time',
        'end_time',
        'created_at',
    ];

    public $timestamps = false;
}
