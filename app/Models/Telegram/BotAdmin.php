<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BotAdmin extends Model
{
    use HasFactory;

    protected $table = 'bot_admin';
    protected $fillable = [
        'user_id',
        'admin_name',
        'step',
        'temp',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'id');
    }
}
