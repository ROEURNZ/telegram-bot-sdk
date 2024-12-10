<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BotController extends Controller
{
    public function system(Request $request)
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

                $result = app('send_photo')->sendPhoto($photoPath, $chat_id, $pcaption);
                $result = app('send_video')->sendVideo($video, $chat_id, $vcaption);
                $result = app('send_text')->sendText($vcaption, $chat_id);


                if ($result['success']) {
                    return response()->json(['message' => 'Photo sent successfully!']);
                } else {
                    Log::error('Failed to send photo: ' . $result['error']);
                    return response()->json(['error' => 'Failed to send photo'], 500);
                }
            } elseif ($text === '/cmd') {

                $result = app('system_command')->setCommandMenu();
            } else {

                $unknowncmd = 'Unknown Command.';
                $unknowntxt = 'Unknown Text.';
                $unknownuser = 'Unknown User.';
                $unknownid = 'Unknown User ID.';

                // Check if the text is a command starting with a forward slash
                if (preg_match('/^\/\w+/', $text)) {
                    // If it's a command but not recognized, send the Unknown Command message
                    $result = app('send_text')->sendText($unknowncmd, $chat_id);
                } elseif (preg_match('/^\d{10}$/', $text)) {
                    // Check if the text is a 10-digit number (user ID or phone number)
                    $result = app('send_text')->sendText($unknownid, $chat_id);
                } elseif (preg_match('/^@([a-zA-Z0-9_]+)$/', $text)) {
                    // Check if the text starts with @ followed by valid characters (user mention)
                    // The pattern matches @ followed by alphanumeric characters or underscores
                    $result = app('send_text')->sendText($unknownuser, $chat_id);
                } else {
                    // If the input does not match any known pattern, treat it as unknown text
                    $result = app('send_text')->sendText($unknowntxt, $chat_id);
                }
            }
        }
    }
}
