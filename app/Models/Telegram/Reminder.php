<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
    use HasFactory;

    protected $table = 'reminder';
    protected $fillable = [
        'user_id',
        'type',
        'start_time',
        'end_time',
        'sent',
        'reply',
        'response',
        'reminder_msg',
        'reminder_button',
        'reminder_num',
        'created_at',
    ];

    // Reminder belongs to User
    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }
}
