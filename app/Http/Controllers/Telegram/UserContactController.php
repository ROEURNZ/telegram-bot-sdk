<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;  // Ensure you import the User model
use App\Telegram\Queries\CallbackShareContact;

class UserContactController extends Controller
{
    protected $callbackHandler;

    public function __construct(CallbackShareContact $callbackHandler)
    {
        $this->callbackHandler = $callbackHandler;
    }

    public function requestContact($chatId)
    {
        $contactService = app('shareContactButton');
        $contactService->shareContactButton($chatId);

        return response()->json(['message' => 'Contact request sent.']);
    }

    public function handleContact(Request $request)
    {
        if (isset($request->message)) {
            $chat_id = $request->message['from']['id'] ?? null;
            $message = $request->message;
            
            // Check if the message contains contact information
            if (isset($message['contact'])) {
                $contact = $message['contact'];
                $chat_id = $message['from']['id'] ?? null;
                $username = $message['from']['username'] ?? '';
                $userUrl = "https://t.me/{$username}";

                $phoneNumber = $contact['phone_number'] ?? null;

                // Find the user in the database using their chat ID
                $user = User::where('chat_id', $chat_id)->first();

                // If user exists, update their phone number
                if ($user) {
                    $user->phone_number = $phoneNumber;
                    $user->save();
                } else {
                    // If user doesn't exist, create a new record
                    $user = new User();
                    $user->chat_id = $chat_id;
                    $user->phone_number = $phoneNumber;
                    $user->username = $username;
                    $user->save();
                }

                // Format user details into a response message
                $userDetails = [
                    'Phone Number' => $contact['phone_number'] ?? '',
                    'First Name' => $contact['first_name'] ?? '',
                    'Last Name' => $contact['last_name'] ?? '',
                    'Username' => $userUrl,
                ];

                $responseText = "Thank you for sharing your contact information.\n";
                foreach ($userDetails as $key => $value) {
                    $responseText .= "{$key}: {$value}\n";
                }

                // Send the contact details and remove the keyboard
                app('sendcontact')->sendContact(
                    $chat_id,
                    $responseText,
                    env('TELEGRAM_BOT_TOKEN'),
                    json_encode(['remove_keyboard' => true])
                );
                app('buttonClockIn')->clockInButton($chat_id);
                return response()->json(['message' => 'Contact information processed and saved.']);
            }
        }
    }

    public function shareContactHandler(Request $request)
    {
        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
    }
}
