<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';
    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'tg_username',
        'phone',
        'email',
        'approval_status',
        'notification_new_user_msg_id',
        'list_emp_msg_id',
        'photo_message_id',
        'photo_id',
        'day_selected_msg_id',
        'set_start_time_msg_id',
        'set_end_time_msg_id',
        'branch_id',
        'branch_name',
        'step',
        'is_step_complete',
        'lang',
        'trigger_alarm',
        'jobdesc',
        'notes',
        'can_break',
        'break_step',
        'can_visit',
        'visit_alert',
        'ping_module',
        'approved_by',
        'created_at',
    ];

    public $timestamps = false;

    // UserProfile belongs to Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // UserProfile has many daily tasks
    public function dailyTasks()
    {
        return $this->hasMany(UserDailyTask::class);
    }

    // UserProfile has many clock-in/out records
    public function clockInOuts()
    {
        return $this->hasMany(UserClockInOut::class);
    }

    // UserProfile has many breaks
    public function breaks()
    {
        return $this->hasMany(UserBreak::class);
    }

    // UserProfile has many reminders
    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
