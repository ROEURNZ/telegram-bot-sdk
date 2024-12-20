<?php

namespace App\Http\Controllers\Telegram;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Telegram\TelegramUser;
use App\Telegram\Queries\CallbackShareContact;

class BotController extends Controller
{
    protected $callbackHandler;
    protected $userContactController;
    protected $botLanguageController;

    public function __construct(
        CallbackShareContact $callbackHandler,
        UserContactController $userContactController,
        BotLanguageController $botLanguageController
    ) {
        $this->callbackHandler = $callbackHandler;
        $this->userContactController = $userContactController;
        $this->botLanguageController = $botLanguageController;
    }

    public function system(Request $request)
    {
        // Check if the request contains a message
        if (isset($request->message)) {
            $chatId      = $request->message['chat']['id'];
            $userId      = $request->message['from']['id'];
            $messageId   = $request->message['message_id'];
            $firstName   = $request->message['from']['first_name'];
            $lastName    = $request->message['from']['last_name'] ?? '';
            $username    = $request->message['from']['username'] ?? '';
            $language    = $request->message['from']['language'] ?? 'en';
            $timestamp   = now();
            $text        = $request->message['text'] ?? null;

            // Handle the /start command
            if ($text === '/start') {


                $user = TelegramUser::where('telegram_id', $userId)->first();

                // Cache chat_id for 60 minutes
                // cache()->put("chat_id_{$chatId}", true, now()->addMinutes(60));

                // Send a welcome message to the user
                $welcome = 'Welcome to the E-TRAX System! You can interact with me now.';


                if (isset($user->telegram_id)) {
                    $welcome2 = sprintf(
                        'Welcome, <strong>%s %s</strong>! Glad to have you here!',
                        $user->first_name,
                        $user->last_name,
                    );
                    $result = app('sendmessage')->sendMessages($welcome2, $chatId);
                } else {
                    $result = app('sendmessage')->sendMessages($welcome, $chatId);
                }

                // Change the language if necessary

                if (empty($user->language)) {
                    $this->botLanguageController->selectLanguage($chatId);
                }

                // $users = User::all();
                // Log::info('hhhhhhhhhhhhh ' . json_encode($users));
                // $userCount = User::count();
                // Log::info("Number of users: {$userCount}");



                // Check if the user already exists in the database

                $params = [
                    'chat_id'     => $chatId,
                    'telegram_id' => $userId,
                    'message_id'  => $messageId,
                    'first_name'  => $firstName,
                    'last_name'   => $lastName,
                    'username'    => $username,
                    'language'    => $language,
                    'date'        => $timestamp,
                ];

                if (!$user) {
                    // Create a new user if they do not exist
                    TelegramUser::create($params);
                    // Set the command menu immediately after creating the user
                    app('usercommandmenu')->setCommandMenu();
                } else {
                    // Set the command menu for the user
                    app('usercommandmenu')->setCommandMenu();
                    // Update the existing user

                    // $user->update($params);
                }


                // Return response after sending the message
                return $result['success']
                    ? response()->json(['message' => 'Welcome message sent successfully!'])
                    : response()->json(['error' => 'Failed to send welcome message'], 500);
            }

            // Handle the /sharecontact command
            if ($text === '/sharecontact') {
                $user = TelegramUser::where('telegram_id', $userId)->first();
                if ($user) {
                    if (empty($user->phone_number)) {
                        return $this->userContactController->requestContact($chatId);
                    } else {
                        // User has already shared their contact info
                        $phoneExist = 'Your contact information has already been shared.';
                        app('sendmessage')->sendMessages($phoneExist, $chatId);
                    }
                } else {
                    // Respond with an error message if the user doesn't exist
                    $unauthenticated = 'You need to be registered first to share your contact information.';
                    app('sendmessage')->sendMessages($unauthenticated, $chatId);
                }
            }

            // Handle contact sharing from the user
            if (isset($request->message['contact'])) {
                return $this->userContactController->handleContact($request);
            }

            // Handle the /changelanguage command
            if ($text === '/changelanguage') {
                $user = TelegramUser::where('telegram_id', $userId)->first();

                if ($user && isset($user->language)) {
                    return $this->botLanguageController->changeLanguage($chatId);
                }
            }

            // Handle language selection (English or Khmer)
            if ($text === '🇺🇸 𝗘𝗻𝗴𝗹𝗶𝘀𝗵' || $text === '🇰🇭 𝗞𝗵𝗺𝗲𝗿') {
                return $this->botLanguageController->handleLanguageResponse($chatId, $text);
            }
        }

        // Handle callback queries if present
        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
        return response()->json(['message' => 'Invalid request.']);
    }
}
