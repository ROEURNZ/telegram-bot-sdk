<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserClockIn;
use App\Models\UserClockInOut;
use App\Models\UserClockOut;
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
                $officeLat = 12.345678;  // Replace with actual latitude
                $officeLon = 98.765432;  // Replace with actual longitude

                // Distance calculation
                $distance = $this->calculateDistance($latitude, $longitude, $officeLat, $officeLon);
                $locationStatus = $distance <= 50 ? 'WITHIN RANGE' : 'OUT OF RANGE';

                // Save Clock-In data
                UserClockIn::create([
                    'user_id' => $chatId,
                    'clock_in_day' => date('l'),
                    'clock_in_lat' => $latitude,
                    'clock_in_lon' => $longitude,
                    'clock_in_distance' => $distance,
                    'clock_in_location_status' => $locationStatus,
                    'clock_in_location_msg_id' => $request->message['message_id'],
                    'clock_in_time_status' => app('buttonClockIn')->getClockInStatus(now()->format('H:i')),
                    'is_clock_in' => true, // Mark as clocked in
                ]);

                // Send message to request a selfie
                app('sendmessage')->sendMessages("📷 Please share a selfie to complete clock-in.", $chatId);
            }

            // Handle Clock-In Selfie
            elseif (isset($request->message['photo']) && cache()->has("clock_in_{$chatId}")) {
                $userClockIn = UserClockIn::where('user_id', $chatId)
                    ->where('clock_in_day', date('l'))
                    ->first();

                if ($userClockIn) {
                    // Get the highest resolution photo's file_id
                    $photos = $request->message['photo'];
                    $fileId = end($photos)['file_id'];

                    // Update clock-in with selfie
                    $userClockIn->clock_in_selfie_msg_id = $fileId;
                    $userClockIn->clock_in_time = now()->format('H:i');
                    $userClockIn->save();

                    // Confirmation Message
                    app('sendmessage')->sendMessages("✅ Clock-In recieved at " . now()->format('h:i A'), $chatId);
                    app('buttonClockOut')->clockOutButton($chatId);
                }

                cache()->forget("clock_in_{$chatId}"); // Clear clock-in state
            }

            // ================================
            // Handle Clock-Out Start
            // ================================
            elseif ($text === '🔴 Clock Out 🔴') {
                $userClockIn = UserClockIn::where('user_id', $chatId)
                    ->where('clock_in_day', date('l'))
                    ->where('is_clock_in', true) // Check if the user has clocked in
                    ->first();

                if (!$userClockIn) {
                    // If the user hasn't clocked in, send a message saying they need to clock in first
                    app('sendmessage')->sendMessages("❌ You need to clock in first before clocking out.", $chatId);
                    return;
                }

                app('buttonClockOut')->clockOutKeyboard($chatId);
            }
            // Handle Confirmation for Clock-Out
            elseif ($text === '✅ Yes') {
                $userClockIn = UserClockIn::where('user_id', $chatId)
                    ->where('clock_in_day', date('l'))
                    ->where('is_clock_in', true) // Check if the user has clocked in
                    ->first();

                if (!$userClockIn) {
                    // If the user hasn't clocked in, send a message saying they need to clock in first
                    app('sendmessage')->sendMessages("❌ You need to clock in first before clocking out.", $chatId);
                    return;
                }
                $message = "🗺 Please share your LIVE location to clock out.";
                app('sendmessage')->sendMessages($message, $chatId);

                // Cache state to track that the user is clocking out
                cache()->put("clock_out_{$chatId}", true, now()->addMinutes(5));
                cache()->forget("clock_in_{$chatId}"); // Ensure clock-in state is cleared
            } elseif ($text === '❌ No') {
                $message = "❌ Clock-Out process canceled. Thank you!";
                app('sendmessage')->sendMessages($message, $chatId);

                // Do not set clock-out state in cache if user selects "No"
                cache()->forget("clock_out_{$chatId}");
            }

            // Handle Clock-Out location
            elseif (isset($request->message['location']) && cache()->has("clock_out_{$chatId}")) {
                $latitude = $request->message['location']['latitude'];
                $longitude = $request->message['location']['longitude'];
                $officeLat = 12.345678;  // Replace with actual latitude
                $officeLon = 98.765432;  // Replace with actual longitude
                // Distance calculation
                $distance = $this->calculateDistance($latitude, $longitude, $officeLat, $officeLon);
                $locationStatus = $distance <= 50 ? 'WITHIN RANGE' : 'OUT OF RANGE';


                // Save Clock-Out data
                UserClockOut::create([
                    'user_id' => $chatId,
                    'clock_out_day' => date('l'),
                    'clock_out_lat' => $latitude,
                    'clock_out_lon' => $longitude,
                    'clock_in_distance' => $distance,
                    'clock_out_location_status' => $locationStatus, // Assuming it's within range
                    'clock_out_location_msg_id' => $request->message['message_id'],
                    'clock_out_time_status' => app('buttonClockOut')->getClockOutStatus(now()->format('H:i')),
                    'is_clock_out' => true, // Mark as clocked out
                ]);

                // Send message to request a selfie
                app('sendmessage')->sendMessages("📷 Please share a selfie to complete clock-out.", $chatId);
            }

            // Handle Clock-Out Selfie
            elseif (isset($request->message['photo']) && cache()->has("clock_out_{$chatId}")) {
                $userClockOut = UserClockOut::where('user_id', $chatId)
                    ->where('clock_out_day', date('l'))
                    ->first();

                if ($userClockOut) {
                    // Get the highest resolution photo's file_id
                    $photos = $request->message['photo'];
                    $fileId = end($photos)['file_id'];

                    // Update clock-out with selfie
                    $userClockOut->clock_out_selfie_msg_id = $fileId;
                    $userClockOut->clock_out_time = now()->format('H:i');
                    $userClockOut->save();

                    // Confirmation Message
                    app('sendmessage')->sendMessages("☑️ Clock-Out recieved at " . now()->format('h:i A'), $chatId);
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

            if ($text == '/manage') {
                return response()->json(app('manage_visit')->handleManageCommand($chatId));
            }
        }

        // Handle callback queries if present
        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
    }

    // Distance calculation logic
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;  // Earth radius in meters

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;  // Result in meters
        return $distance;
    }
}
