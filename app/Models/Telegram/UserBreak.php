<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBreak extends Model
{
    use HasFactory;

    protected $table = 'user_break';
    protected $fillable = [
        'user_id',
        'break_day',
        'break_time',
        'location_status',
        'location_lat',
        'location_lon',
        'location_msg_id',
        'location_distance',
        'selfie_msg_id',
        'break_action',
        'created_at',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }
}
