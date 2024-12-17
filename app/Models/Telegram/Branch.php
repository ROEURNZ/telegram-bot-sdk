<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch';
    protected $fillable = [
        'branch_name',
        'branch_lat',
        'branch_lon',
    ];

    public $timestamps = false;

    // A Branch has many users (assuming employees are assigned to branches)
    public function users()
    {
        return $this->hasMany(UserProfile::class);
    }
}
