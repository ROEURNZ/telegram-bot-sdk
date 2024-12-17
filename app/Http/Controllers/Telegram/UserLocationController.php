<?php

namespace App\Http\Controllers\Telegram;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserLocationController extends Controller{
    public function handleLocation(Request $request)
    {
        if (isset($request->message)) {
            $chatId = $request->message['from']['id'] ?? null;
            $message = $request->message;
            // Handle location sharing
            if (isset($request->message['location'])) {
                $location = $request->message['location'];
                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
                Log::info("Location: lat: {$latitude}, long: {$longitude}");
                $message = "📷 Please share a selfie to clock in.";
                app('sendmessage')->sendMessages($message, $chatId);
                
            }
        }
    }
}