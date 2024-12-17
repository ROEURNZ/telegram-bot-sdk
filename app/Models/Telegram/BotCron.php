<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BotCron extends Model
{
    use HasFactory;

    protected $table = 'bot_cron';
    protected $fillable = [
        'title',
        'cron_file',
        'cron_command',
        'cron_config',
        'cron_active',
        'last_run',
    ];

    public $timestamps = false;

    // BotCron can have many logs (assuming you might want logs related to it)
    public function logs()
    {
        return $this->hasMany(RequestReplyLog::class);
    }
}
