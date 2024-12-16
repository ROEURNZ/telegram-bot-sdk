<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Telegram\Queries\CallbackShareContact;

class UserContactController extends Controller
{

    protected $callbackHandler;
    protected $userContactController;
    protected $botLanguageController;

    public function __construct(
        CallbackShareContact $callbackHandler,
    ) {
        $this->callbackHandler = $callbackHandler;

    }
    public function requestContact($chatId)
    {
        $contactService = app('requestShareContact');
        $contactService->askContactInfo($chatId);

        return response()->json(['message' => 'Contact request sent.']);
    }

    public function handleContact(Request $request)
    {
        $chatId    = $request->message['chat']['id'];
        $contact   = $request->message['contact'];
        $firstName = $contact['first_name'];
        $lastName  = $contact['last_name'] ?? '';
        $phoneNumber = $contact['phone_number'];
        $username = $request->message['from']['username'] ?? null;

        $userUrl = $username ? "https://t.me/{$username}" : 'Username not available';

        $responseMessage = sprintf(
            "Thank you for sharing your contact info.
            \nName: <b>%s</b> %s\nPhone: %s\nUsername: %s",
            $firstName,
            $lastName,
            $phoneNumber,
            $userUrl
        );

        // Send the contact details and remove the keyboard
        app('sendcontact')->sendContact(
            $chatId,
            $responseMessage,
            env('TELEGRAM_BOT_TOKEN'),
            json_encode(['remove_keyboard' => true])
        );

        return response()->json(['message' => 'Contact info processed.']);
    }

    public function shareContactHandler(Request $request)
    {

        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
    }
}
