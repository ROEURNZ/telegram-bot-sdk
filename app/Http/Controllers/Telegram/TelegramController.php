
// namespace App\Http\Controllers\Telegram;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;

// class TelegramController extends Controller
// {
//     public function inbound(Request $request)
//     {
//         // Log the entire incoming request for debugging
//         Log::info($request->all());

//         // Check if the 'message' key exists in the request
//         if (isset($request->message)) {
//             // Get user details
//             $chat_id = $request->message['from']['id'] ?? null;
//             $firstName = $request->message['from']['first_name'] ?? '';
//             $lastName = $request->message['from']['last_name'] ?? '';
//             $reply_to_message = $request->message['message_id'] ?? null;
//             $text = $request->message['text'] ?? null;

//             // Log chat details
//             Log::info("chat_id: {$chat_id}");
//             Log::info("reply_to_message: {$reply_to_message}");

//             // Check if chat_id is present
//             if (!$chat_id) {
//                 return response()->json(['error' => 'Chat ID not found'], 400);
//             }

//             // Handle the /start command
//             if ($text === '/start') {
//                 $welcomeMessage = "Welcome to the e-Trax System bot, <b>{$firstName} {$lastName}</b>";

//                 // Cache the chat_id to mark that the user has received the welcome message
//                 cache()->put("chat_id_{$chat_id}", true, now()->addMinutes(60));

//                 // Send the welcome photo with caption
//                 $photoPath = public_path('images/app.png');
//                 $video = public_path('videos/app.mp4');
//                 $pcaption = 'Welcome to the e-Trax System bot! 🤖';
//                 $vcaption = 'Welcome to E TRAX time attendance bot. Please use start button or /start to join our system.';

//                 $result = app('telegram_bot')->sendPhoto($photoPath, $chat_id, $pcaption);
//                 $result = app('telegram_bot')->sendVideo($video, $chat_id, $vcaption);
//                 $result = app('telegram_bot')->sendText($welcomeMessage, $chat_id);


//                 if ($result['success']) {
//                     return response()->json(['message' => 'Photo sent successfully!']);
//                 } else {
//                     Log::error('Failed to send photo: ' . $result['error']);
//                     return response()->json(['error' => 'Failed to send photo'], 500);
//                 }
//             }
//             // Handle the /image command
//             elseif ($text === '/image') {
//                 $responseText = "Please upload an IMAGE and enjoy the magic 🪄";
//                 $result = app('telegram_bot')->replyMessage($responseText, $chat_id, $reply_to_message);

//                 if ($result['success']) {
//                     return response()->json(['message' => 'Image instruction sent successfully!']);
//                 } else {
//                     Log::error('Failed to send message: ' . $result['error']);
//                     return response()->json(['error' => 'Failed to send message'], 500);
//                 }
//             }
//             // Handle photo uploads
//             elseif (isset($request->message['photo'])) {
//                 // Get the image URL
//                 $image_url = app('telegram_bot')->getImageUrl($request->message['photo']);

//                 // Log the image URL
//                 Log::info("Image URL: {$image_url}");

//                 // Extract text from the image
//                 $detectedText = app('image_detect_text')->getTextFromImage($image_url);

//                 $responseText = empty($detectedText)
//                     ? "<b>Tab 🤖:</b> No text detected in the image."
//                     : "<b>Detected Text:</b> {$detectedText}";

//                 // Send the response with HTML formatting
//                 $result = app('telegram_bot')->sendText($responseText, $chat_id);

//                 if ($result['success']) {
//                     return response()->json(['message' => 'Text response sent successfully!']);
//                 } else {
//                     Log::error('Failed to send text response: ' . $result['error']);
//                     return response()->json(['error' => 'Failed to send text response'], 500);
//                 }
//             }
//             // Handle other cases
//             else {
//                 $responseText = "<b>Tab 🤖:</b> Please upload an <b>IMAGE!</b>";
//                 $result = app('telegram_bot')->sendText($responseText, $chat_id);

//                 if ($result['success']) {
//                     return response()->json(['message' => 'Default message sent successfully!']);
//                 } else {
//                     Log::error('Failed to send default message: ' . $result['error']);
//                     return response()->json(['error' => 'Failed to send default message'], 500);
//                 }
//             }
//         } else {
//             // Handle invalid requests
//             Log::warning('Invalid request format');
//             return response()->json(['error' => 'Invalid request format'], 400);
//         }
//     }
// }





namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
class BotController extends Controller
{
    public function inbound(Request $request)
    {
        // Log the entire incoming request for debugging
        Log::info($request->all());

        // Check if the 'message' key exists in the request
        if (isset($request->message)) {
            // Get user details
            $chat_id = $request->message['from']['id'] ?? null;
            $firstName = $request->message['from']['first_name'] ?? '';
            $lastName = $request->message['from']['last_name'] ?? '';
            $reply_to_message = $request->message['message_id'] ?? null;
            $text = $request->message['text'] ?? null;

            // Log chat details
            Log::info("chat_id: {$chat_id}");
            Log::info("reply_to_message: {$reply_to_message}");

            // Check if chat_id is present
            if (!$chat_id) {
                return response()->json(['error' => 'Chat ID not found'], 400);
            }

            // Handle the /start command
            if ($text === '/start') {
                $welcomeMessage = "Welcome to the e-Trax System bot, <b>{$firstName} {$lastName}</b>";

                // Cache the chat_id to mark that the user has received the welcome message
                cache()->put("chat_id_{$chat_id}", true, now()->addMinutes(60));

                // Send the welcome photo with caption
                $photoPath = public_path('images/app.png');
                $video = public_path('videos/app.mp4');
                $pcaption = 'Welcome to the e-Trax System bot! 🤖';
                $vcaption = 'Welcome to E TRAX time attendance bot. Please use start button or /start to join our system.';

                // $result = app('send_photo')->sendPhoto($photoPath, $chat_id, $pcaption);
                $result = app('send_video')->sendVideo($video, $chat_id, $vcaption);
                // $result = app('send_text')->sendText($vcaption, $chat_id);


                if ($result['success']) {
                    return response()->json(['message' => 'Photo sent successfully!']);
                } else {
                    Log::error('Failed to send photo: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send photo'], 500);
                }
            }












            // Handle the /image command
            elseif ($text === '/image') {
                $responseText = "Please upload an IMAGE and enjoy the magic 🪄";
                $result = app('reply_text')->replyMessage($responseText, $chat_id, $reply_to_message);

                if ($result['success']) {
                    return response()->json(['message' => 'Image instruction sent successfully!']);
                } else {
                    Log::error('Failed to send message: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send message'], 500);
                }
            } elseif ($text === 'GSS') {
                $responseText = "Please upload an IMAGE and enjoy the magic 🪄";
                $result = app('reply_text')->replyMessage('GSS E-TRAX Bot (Beta) is the best choice!', $chat_id, $reply_to_message);

                if ($result['success']) {
                    return response()->json(['message' => 'Image instruction sent successfully!']);
                } else {
                    Log::error('Failed to send message: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send message'], 500);
                }
            }


            elseif ($text === '/changelanguage') {

                $result = app('system_buttons')->setKeyboards($chat_id);
                // $result = app('system_buttons')->removeKeyboard($chat_id);
                // $result = app('system_language')->sendLanguageSelection($chat_id);

                // $result = app('send_text')->sendText('remove', $chat_id);
                $result = app('send_text')->sendText( $chat_id, json_encode(['remove_keyboard' => true]));




                if ($result['success']) {
                    return response()->json(['message' => 'Image instruction sent successfully!']);
                } else {
                    Log::error('Failed to send message: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send message'], 500);
                }
            }

            // elseif ($text === 'photo' || 'PHOTO' || 'PT' || 'image' || 'Hello @ROEURN_KAKI') {
            //     $photoPath = public_path('images/app.png');

            //     $pcaption = 'Welcome to the e-Trax System bot! 🤖';
            //     $result = app('send_photo')->sendPhoto($photoPath, $chat_id, $pcaption);

            //     if ($result['success']) {
            //         return response()->json(['message' => 'Image instruction sent successfully!']);
            //     } else {
            //         Log::error('Failed to send message: ' . $result['error']);
            //         return response()->json(['error' => 'Failed to send message'], 500);
            //     }
            // }


            elseif ($text === '/help') {
                $responseText = "What can I help you? 🇰🇭 ";
                // $result = app('telegram_bot')->replyMessage($responseText, $chat_id, $reply_to_message);
                $result = app('reply_text')->replyMessage($responseText, $chat_id, $reply_to_message);

                if ($result['success']) {
                    return response()->json(['message' => 'Image instruction sent successfully!']);
                } else {
                    Log::error('Failed to send message: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send message'], 500);
                }
            }
            // Handle photo uploads
            elseif (isset($request->message['photo'])) {
                // Get the image URL
                $image_url = app('telegram_bot')->getImageUrl($request->message['photo']);

                // Log the image URL
                Log::info("Image URL: {$image_url}");

                // Extract text from the image
                $detectedText = app('image_detect_text')->getTextFromImage($image_url);

                $responseText = empty($detectedText)
                    ? "<b>Tab 🤖:</b> No text detected in the image."
                    : "<b>Detected Text:</b> {$detectedText}";

                // Send the response with HTML formatting
                $result = app('send_text')->sendText($responseText, $chat_id);

                if ($result['success']) {
                    return response()->json(['message' => 'Text response sent successfully!']);
                } else {
                    Log::error('Failed to send text response: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send text response'], 500);
                }
            }
            // Handle other cases
            else {
                $responseText = "<b>Tab 🤖:</b> Please upload an <b>IMAGE!</b>";
                $result = app('send_text')->sendText($responseText, $chat_id);

                if ($result['success']) {
                    return response()->json(['message' => 'Default message sent successfully!']);
                } else {
                    Log::error('Failed to send default message: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send default message'], 500);
                }
            }
        } else {
            // Handle invalid requests
            Log::warning('Invalid request format');
            return response()->json(['error' => 'Invalid request format'], 400);
        }
    }
}
