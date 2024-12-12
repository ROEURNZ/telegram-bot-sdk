<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Telegram\Queries\CallbackHandler;

class BotController extends Controller
{

    protected $callbackHandler;
    // Inject the CallbackHandler via the constructor
    public function __construct(CallbackHandler $callbackHandler)
    {
        $this->callbackHandler = $callbackHandler;
    }

    public function system(Request $request)
    {
        if (isset($request->message)) {

            // Get user details
            $chatId      = $request->message['chat']['id'];
            $userId      = $request->message['from']['id'];
            $messageId   = $request->message['message_id'];
            $firstName   = $request->message['from']['first_name'];
            $lastName    = $request->message['from']['last_name'] ?? '';
            $username    = $request->message['from']['username'] ?? '';
            $language    = $request->message['from']['language'] ?? 'en';
            $timestamps = date('Y-m-d H:i:s');
            $reply_to_message = $messageId;
            $text = $request->message['text'] ?? null;

            // Log chat details
            Log::info("chat_id: {$chatId}");
            Log::info("reply_to_message: {$reply_to_message}");

            // Check if chat_id is present
            if (!$chatId) {
                return response()->json(['error' => 'Chat ID not found'], 400);
            }

            // Handle the /start command
            if ($text === '/start') {
                
            app('usercommandmenu')->setCommandMenu();


                $welcomeMessage = "Welcome to the e-Trax System bot, <b>{$firstName} {$lastName}</b>";

                // Cache the chat_id to mark that the user has received the welcome message
                cache()->put("chat_id_{$chatId}", true, now()->addMinutes(60));

                // Send the welcome photo with caption
                $photoPath = public_path('images/app.png');
                $video = public_path('videos/app.mp4');
                $pcaption = 'Welcome to the e-Trax System bot! 🤖';
                $vcaption = 'Welcome to E TRAX time attendance bot. Please use start button or /start to join our system.';

                // For Send Video for real usage
                // $result = app('sendvideo')->sendVideo($video, $chatId, $vcaption);

                // For Send Text Message for fast test
                $result = app('sendmessage')->sendMessages($vcaption, $chatId);

                // For Send Photo Test 2
                // $result = app('sendphoto')->sendPhoto($photoPath, $chatId, $pcaption);


                if ($result['success']) {
                    return response()->json(['message' => 'Photo sent successfully!']);
                } else {
                    Log::error('Failed to send photo: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send photo'], 500);
                }
                // $result =
            }

            if ($text == '/manage') {
                return response()->json(app('manage_visit')->handleManageCommand($chatId));
            }

            // In BotController's system method
            if ($text === '/sharecontact') {

                $contactService = app('inline_keyboard');
                $contactService->askContactInfo($chatId);

                return response()->json(['message' => 'Contact request sent.']);
            }


            if (isset($request->message['contact'])) {
                $contact = $request->message['contact'];
                $firstName = $contact['first_name'];
                $lastName = $contact['last_name'] ?? '';
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
            }
            //  elseif ($text === '/cmd') {

            //     $result = app('usercommandmenu')->setCommandMenu();
            // }
             elseif ($text === '/changelanguage') {
                $result = app('system_buttons')->setKeyboards($chatId);
            }

            if ($text === '🇺🇸 𝗘𝗻𝗴𝗹𝗶𝘀𝗵' || $text === '🇰🇭 𝗞𝗵𝗺𝗲𝗿') {
                // Handle the language selection and remove the keyboard
                app('system_buttons')->handleUserResponse($chatId, $text);
            }


        }

        // Handle callback queries if present
        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
    }
}
