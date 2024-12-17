<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BotSetting extends Model
{
    use HasFactory;

    protected $table = 'bot_settings';
    protected $fillable = [
        'time_tolerance',
        'location_tolerance',
        'userbreak_req_step',
        'clockuser_req_step',
        'dead_man_feature',
        'dead_man_task_time',
        'welcome_msg',
        'welcome_img',
        'company_email',
        'company_phone',
        'module_visit',
        'module_alert',
        'module_break',
        'clockout_reminder_interval',
        'clockout_reminder_timeout',
    ];

    public $timestamps = false;
}
