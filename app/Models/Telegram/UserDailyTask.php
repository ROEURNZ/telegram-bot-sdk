<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDailyTask extends Model
{
    use HasFactory;

    protected $table = 'user_daily_tasks';
    protected $fillable = [
        'user_id',
        'task_start',
        'task_end',
        'task_send',
        'task_reply',
        'task_status',
        'reply_time',
        'reply_location',
        'reply_location_status',
        'reply_location_distance',
        'reply_location_msg_id',
        'created_at',
    ];

    public $timestamps = false;

    // UserDailyTask belongs to User
    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }
}
