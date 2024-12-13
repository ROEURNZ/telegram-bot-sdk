<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Store a new user in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chat_id' => 'required|unique:users,chat_id',
            'message_id' => 'required|integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:1',
        ]);

        $user = User::create([
            'chat_id' => $validated['chat_id'],
            'message_id' => $validated['message_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'phone_number' => $validated['phone_number'],
            'language' => $validated['language'],
            'password' => Hash::make($validated['password'] ?? 'default_password'),
        ]);

        Log::info('User created with chat_id: ' . $validated['chat_id']);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

}
