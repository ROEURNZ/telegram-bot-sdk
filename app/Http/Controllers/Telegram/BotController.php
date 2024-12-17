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

            // Handle photo upload or selfie
            if (isset($request->message['photo'])) {
                Log::info("Photo received from chat ID: {$chatId}");
                $time = date('h:i A'); // Format time as 12-hour with AM/PM
                $message = "✅ Clock In Received at {$time}";
                app('sendmessage')->sendMessages($message, $chatId);
                app('buttonClockOut')->clockOutButton($chatId);

                // Save the selfie message ID to the database
                if (isset($request->message['message_id'])) {
                    $userClockInOut = UserClockInOut::where('user_id', $chatId)
                        ->where('clock_in_day', date('Y-m-d')) // Ensure you're updating the correct record for today
                        ->first();

                    if ($userClockInOut) {
                        $userClockInOut->clock_in_selfie_msg_id = $request->message['message_id']; // Save the selfie message ID
                        $userClockInOut->save(); // Save the updated record
                    }
                }
            }


            // Handle Clock-In Location Sharing
            if (isset($request->message['location'])) {
                $location = $request->message['location'];
                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
                Log::info("Location: lat: {$latitude}, long: {$longitude}");

                // Calculate the distance from the reference point (e.g., office location)
                // Example: reference point coordinates (office location)
                $referenceLat = 12.345678; // Office Latitude
                $referenceLon = 98.765432; // Office Longitude

                // Calculate distance (e.g., Haversine formula or any other method)
                $distance = $this->calculateDistance($latitude, $longitude, $referenceLat, $referenceLon);

                // Store clock-in data in the database
                $userClockInOut = new UserClockInOut();
                $userClockInOut->user_id = $chatId;  // Use chat_id as user_id
                $userClockInOut->clock_in_day = date('Y-m-d'); // Store today's date
                $userClockInOut->clock_in_location_status = 'Clocked In'; // Status
                $userClockInOut->clock_in_lat = $latitude; // Latitude from the location message
                $userClockInOut->clock_in_lon = $longitude; // Longitude from the location message
                $userClockInOut->clock_in_location_msg_id = $request->message['message_id']; // Save message ID
                $userClockInOut->clock_in_time_status = 'On Time'; // Example, could be 'Late' or 'On Time'
                $userClockInOut->clock_in_time = date('H:i:s'); // Current time of clock-in
                $userClockInOut->is_clock_in = 'Yes'; // Mark as clocked in
                $userClockInOut->clock_in_distance = $distance; // Store the calculated distance
                $userClockInOut->created_at = now(); // Set created timestamp
                $userClockInOut->save(); // Save the clock-in record

                $message = "📷 Please share a selfie to clock in.";
                app('sendmessage')->sendMessages($message, $chatId);
            }




            // Handle specific commands or inputs
            if ($text === '/sharecontact') {
                return $this->userContactController->requestContact($chatId);
            }

            if ($text === '/changelanguage') {
                return $this->botLanguageController->changeLanguage($chatId);
            }

            if ($text === '🟢 Clock In 🟢') {
                $message = "🗺 Please share your LIVE location to clock in.";
                app('sendmessage')->sendMessages($message, $chatId);
            }
            if ($text === '🔴 Clock Out 🔴') {
                $message = "🗺 Please share your LIVE location to clock in.";
                app('sendmessage')->sendMessages($message, $chatId);
            }
        }

        // Handle callback queries if present
        if (isset($request->callback_query)) {
            return $this->callbackHandler->handleCallback($request);
        }
    }
    // Method to calculate distance between two lat/lon points using Haversine formula (or any other method you prefer)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDiff = $latTo - $latFrom;
        $lonDiff = $lonTo - $lonFrom;

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }
}
