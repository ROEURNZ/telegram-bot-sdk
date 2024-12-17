<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduledMessageTime extends Model
{
    use HasFactory;

    protected $table = 'scheduled_messages_time';
    protected $fillable = [
        'message_id',
        'day',
        'time',
        'is_run',
    ];

    public $timestamps = false;

    // ScheduledMessageTime belongs to ScheduledMessage
    public function scheduledMessage()
    {
        return $this->belongsTo(ScheduledMessage::class);
    }
}
