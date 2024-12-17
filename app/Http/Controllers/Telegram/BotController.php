<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserClockInOut;
use App\Telegram\Queries\CallbackShareContact;

date_default_timezone_set('Asia/Phnom_Penh');

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
        if (isset($request->message)) {
            $chatId = $request->message['chat']['id'];
            $text = $request->message['text'] ?? null;

            // Handle the /start command
            if ($text === '/start') {
                // Check if the user already exists in the database
                $existingUser = User::where('chat_id', $chatId)->first();

                // If user does not exist, store new user data
                if (!$existingUser) {
                    $firstName = $request->message['chat']['first_name'];
                    $lastName = $request->message['chat']['last_name'] ?? null;
                    $username = $request->message['chat']['username'] ?? null;
                    $phoneNumber = $request->message['contact']['phone_number'] ?? null;
                    $language = 'en'; // Default language, can be updated later

                    // Create a new user record in the database
                    $user = new User();
                    $user->chat_id = $chatId;
                    $user->message_id = $request->message['message_id'];
                    $user->first_name = $firstName;
                    $user->last_name = $lastName;
                    $user->username = $username;
                    $user->phone_number = $phoneNumber;
                    $user->language = $language;
                    $user->save();
                }

                // Handle user interaction logic
                app('usercommandmenu')->setCommandMenu();
                cache()->put("chat_id_{$chatId}", true, now()->addMinutes(60));

                $vcaption = 'Welcome to E TRAX time attendance bot. Please use the start button or /start to join our system.';
                $result = app('sendmessage')->sendMessages($vcaption, $chatId);
                $this->botLanguageController->changeLanguage($chatId);

                return $result['success']
                    ? response()->json(['message' => 'Photo sent successfully!'])
                    : response()->json(['error' => 'Failed to send photo'], 500);
            }

            if ($text === '🇺🇸 𝗘𝗻𝗴𝗹𝗶𝘀𝗵' || $text === '🇰🇭 𝗞𝗵𝗺𝗲𝗿') {
                return $this->botLanguageController->handleLanguageResponse($chatId, $text);
            }

            if (isset($request->message['contact'])) {
                return $this->userContactController->handleContact($request);
            }

            // ================================
            // Handle Clock In Start
            // ================================
            if ($text === '🟢 Clock In 🟢') {
                $message = "🗺 Please share your LIVE location to clock in.";
                app('sendmessage')->sendMessages($message, $chatId);

                // Cache state to track that the user is clocking in
                cache()->put("clock_in_{$chatId}", true, now()->addMinutes(5));
                cache()->forget("clock_out_{$chatId}"); // Ensure clock-out state is cleared
            }

            // Handle Clock-In location
            elseif (isset($request->message['location']) && cache()->has("clock_in_{$chatId}")) {
                $latitude = $request->message['location']['latitude'];
                $longitude = $request->message['location']['longitude'];

                // Save Clock-In data
                UserClockInOut::updateOrCreate(
                    ['user_id' => $chatId, 'clock_in_day' => date('D')],
                    [
                        'clock_in_lat' => $latitude,
                        'clock_in_lon' => $longitude,
                        'clock_in_location_msg_id' => $request->message['message_id'],
                        'clock_in_time' => now()->format('H:i'),
                        'is_clock_in' => 'clock_in',
                        'clock_in_time_status' => $this->getClockInStatus(now()->format('H:i')),
                    ]
                );


                // Send message to request a selfie
                app('sendmessage')->sendMessages("📷 Please share a selfie to complete clock-in.", $chatId);
            }

            // Handle Clock-In Selfie
            elseif (isset($request->message['photo']) && cache()->has("clock_in_{$chatId}")) {
                $userClockInOut = UserClockInOut::where('user_id', $chatId)
                    ->where('clock_in_day', date('D'))
                    ->first();

                if ($userClockInOut) {
                    $userClockInOut->clock_in_selfie_msg_id = $request->message['message_id'];
                    $userClockInOut->save();

                    // Confirmation Message
                    app('sendmessage')->sendMessages("✅ Clock-In completed at " . now()->format('h:i A'), $chatId);
                    app('buttonClockOut')->clockOutButton($chatId);
                }

                cache()->forget("clock_in_{$chatId}"); // Clear clock-in state
            }

            // ================================
            // Handle Clock Out Start
            // ================================
            elseif ($text === '🔴 Clock Out 🔴') {
                app('buttonClockOut')->clockOutKeyboard($chatId);
            } elseif ($text === '✅ Yes') {
                $message = "🗺 Please share your LIVE location to clock in.";
                app('sendmessage')->sendMessages($message, $chatId);
                // Cache state to track that the user is clocking out
                cache()->put("clock_out_{$chatId}", true, now()->addMinutes(5));
                cache()->forget("clock_in_{$chatId}"); // Ensure clock-in state is cleared

            }

            // Handle Clock-Out location
            elseif (isset($request->message['location']) && cache()->has("clock_out_{$chatId}")) {
                $latitude = $request->message['location']['latitude'];
                $longitude = $request->message['location']['longitude'];

                // Save Clock-Out data
                $userClockInOut = UserClockInOut::updateOrCreate(
                    ['user_id' => $chatId, 'clock_in_day' => date('D')],
                    [
                        'clock_out_lat' => $latitude,
                        'clock_out_lon' => $longitude,
                        'clock_out_location_msg_id' => $request->message['message_id'],
                        'clock_out_time' => now()->format('H:i'), // Clock-Out Time
                        'is_clock_in' => 'clock_out',
                    ]
                );

                // Send message to request a selfie
                app('sendmessage')->sendMessages("📷 Please share a selfie to complete clock-out.", $chatId);
            }

            // Handle Clock-Out Selfie
            elseif (isset($request->message['photo']) && cache()->has("clock_out_{$chatId}")) {
                $userClockInOut = UserClockInOut::where('user_id', $chatId)
                    ->where('clock_in_day', date('D'))
                    ->first();

                if ($userClockInOut) {
                    $userClockInOut->clock_in_selfie_msg_id = $request->message['message_id'];
                    $userClockInOut->save();

                    // Confirmation Message
                    app('sendmessage')->sendMessages("☑️ Clock-Out completed at " . now()->format('h:i A'), $chatId);
                }

                cache()->forget("clock_out_{$chatId}"); // Clear clock-out state
            }




            // Handle specific commands or inputs
            if ($text === '/sharecontact') {
                return $this->userContactController->requestContact($chatId);
            }

            if ($text === '/changelanguage') {
                return $this->botLanguageController->changeLanguage($chatId);
            }
        }

        // Handle callback queries if present
        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
    }
    private function getClockInStatus($currentTime)
    {
        $workStartTime = '09:00'; // Example start time
        return $currentTime > $workStartTime ? 'LATE' : 'ON TIME';
    }
}
