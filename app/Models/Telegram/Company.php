<?php

namespace App\Models\Telegram;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use SoftDeletes;

    // Mass assignable attributes
    protected $fillable = [
        'name',
        'email',
        'logo',
        'address'
    ];

    /**
     * Relationship: A company can have many branches.
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'company_id', 'id');
    }
}
