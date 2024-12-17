<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $table = 'scheduled_messages';
    protected $fillable = [
        'title',
        'message',
        'destination',
        'media_type',
        'media',
        'runtime',
        'last_run',
        'created_by',
        'created_at',
    ];

    public $timestamps = false;

    public function times()
    {
        return $this->hasMany(ScheduledMessageTime::class);
    }
}
