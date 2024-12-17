<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;

class BotLanguageController extends Controller
{
    public function changeLanguage($chatId)
    {
        $result = app('buttonSelectLanguage')->setKeyboards($chatId);
        return response()->json(['message' => 'Language selection keyboard sent.']);
    }

    public function handleLanguageResponse($chatId, $text)
    {
        app('buttonSelectLanguage')->handleUserResponse($chatId, $text);
        return response()->json(['message' => 'Language preference updated.']);
    }
}
