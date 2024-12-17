<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestReplyLog extends Model
{
    use HasFactory;

    protected $table = 'request_reply_log';
    protected $fillable = [
        'user_id',
        'user_request',
        'bot_reply',
        'api_request_url',
        'api_response',
        'wrong_reply_user_stat',
        'created_at',
    ];

    public $timestamps = false;

    // RequestReplyLog belongs to a User
    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }

    // RequestReplyLog belongs to a BotCron (optional, if you need to log the cron)
    public function botCron()
    {
        return $this->belongsTo(BotCron::class);
    }
}
