<?php

// namespace App\Http\Controllers\Telegram;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;
// use App\Telegram\Queries\CallbackHandler;

// class RecoveryBotController extends Controller
// {

//     protected $callbackHandler;
//     // Inject the CallbackHandler via the constructor
//     public function __construct(CallbackHandler $callbackHandler)
//     {
//         $this->callbackHandler = $callbackHandler;
//     }

//     public function recoverysystem(Request $request)
//     {
//         if (isset($request->message)) {
//             $text = $request->message['text'] ?? null;
//             // Log::info("Text: {$text}");

//             // Get user details
//             $chatId      = $request->message['chat']['id'];
//             $userId      = $request->message['from']['id'];
//             $messageId   = $request->message['message_id'];
//             $firstName   = $request->message['from']['first_name'];
//             $lastName    = $request->message['from']['last_name'] ?? '';
//             $username    = $request->message['from']['username'] ?? '';
//             $language    = $request->message['from']['language'] ?? 'en';
//             $timestamps = date('Y-m-d H:i:s');
//             $reply_to_message = $messageId;

//             // Log chat details
//             Log::info("chat_id: {$chatId}");
//             Log::info("reply_to_message: {$reply_to_message}");

//             // Check if chat_id is present
//             if (!$chatId) {
//                 return response()->json(['error' => 'Chat ID not found'], 400);
//             }

//             // Handle the /start command
//             if ($text === '/start') {

//                 app('usercommandmenu')->setCommandMenu();

//                 cache()->put("chat_id_{$chatId}", true, now()->addMinutes(60));

//                 // Send the welcome photo with caption
//                 $video = public_path('videos/app.mp4');
//                 $vcaption = 'Welcome to E TRAX time attendance bot. Please use start button or /start to join our system.';

//                 // $result = app('sendvideo')->sendVideo($video, $chatId, $vcaption);
//                 $result = app('sendmessage')->sendMessages($vcaption, $chatId);
//                 app('system_buttons')->setKeyboards($chatId);

//                 if ($result['success']) {
//                     return response()->json(['message' => 'Photo sent successfully!']);
//                 } else {
//                     Log::error('Failed to send photo: ' . $result['error']);
//                     return response()->json(['error' => 'Failed to send photo'], 500);
//                 }
//             }

//             if ($text == '/manage') {
//                 return response()->json(app('manage_visit')->handleManageCommand($chatId));
//             }

//             // In BotController's system method
//             if ($text === '/sharecontact') {

//                 $contactService = app('inline_keyboard');
//                 $contactService->askContactInfo($chatId);

//                 return response()->json(['message' => 'Contact request sent.']);
//             }


//             if (isset($request->message['contact'])) {
//                 $contact = $request->message['contact'];
//                 $firstName = $contact['first_name'];
//                 $lastName = $contact['last_name'] ?? '';
//                 $phoneNumber = $contact['phone_number'];
//                 $username = $request->message['from']['username'] ?? null;

//                 $userUrl = $username ? "https://t.me/{$username}" : 'Username not available';

//                 $responseMessage = sprintf(
//                     "Thank you for sharing your contact info.
//                     \nName: <b>%s</b> %s\nPhone: %s\nUsername: %s",
//                     $firstName,
//                     $lastName,
//                     $phoneNumber,
//                     $userUrl
//                 );

//                 // Send the contact details and remove the keyboard
//                 app('sendcontact')->sendContact(
//                     $chatId,
//                     $responseMessage,
//                     env('TELEGRAM_BOT_TOKEN'),
//                     json_encode(['remove_keyboard' => true])
//                 );
//             }

//             elseif ($text === '/changelanguage') {
//                 $result = app('system_buttons')->setKeyboards($chatId);
//             }

//             if ($text === '🇺🇸 𝗘𝗻𝗴𝗹𝗶𝘀𝗵' || $text === '🇰🇭 𝗞𝗵𝗺𝗲𝗿') {
//                 // Handle the language selection and remove the keyboard
//                 app('system_buttons')->handleUserResponse($chatId, $text);
//             }
//         }

//         // Handle callback queries if present for contact share
//         if (isset($request->callback_query)) {
//             return $this->callbackHandler->handleCallback($request);
//         }
//     }
// }