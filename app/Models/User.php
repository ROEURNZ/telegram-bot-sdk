<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'user_id',
        'message_id',
        'first_name',
        'last_name',
        'username',
        'phone_number',
        'language',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if a user exists based on user_id, username, or phone_number.
     *
     * @param array $params
     * @return bool
     */
    public static function userExists(array $params): bool
    {
        return self::where('user_id', $params['user_id'])
            ->orWhere('username', $params['username'] ?? '')
            ->orWhere('phone_number', $params['phone_number'] ?? '')
            ->exists();
    }

    /**
     * Register a new user if they don't already exist.
     *
     * @param array $params
     * @return User|string
     */
    public static function registerUser(array $params)
    {
        // Check if the user already exists
        if (self::userExists($params)) {
            return "Error: User already exists.";
        }

        // Create and return the new user
        return self::create([
            'user_id'      => $params['user_id'],
            'chat_id'      => $params['chat_id'],
            'message_id'   => $params['message_id'],
            'first_name'   => $params['first_name'],
            'last_name'    => $params['last_name'] ?? null,
            'username'     => $params['username'] ?? null,
            'phone_number' => $params['phone_number'] ?? null,
            'language'     => $params['language'] ?? 'en',
        ]);
    }
}
