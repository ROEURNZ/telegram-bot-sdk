<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserVisit extends Model
{
    use HasFactory;

    protected $table = 'user_visits';
    protected $fillable = [
        'user_id',
        'visit_day',
        'visit_time',
        'visit_lat',
        'visit_lon',
        'visit_location_msg_id',
        'visit_selfie_msg_id',
        'visit_notes',
        'visit_action',
        'created_at',
    ];

    public $timestamps = false;
}
