<?php

namespace App\Models\Telegram;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch';
    protected $fillable = [
        'name',
        'company_id',
        'branch_lat',
        'branch_lon',
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }
}
