<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BotLanguageController extends Controller
{
    public function changeLanguage($chatId)
    {
        app('buttonSelectLanguage')->setKeyboards($chatId);
        return response()->json(['message' => 'Language selection keyboard sent.']);
    }

    public function handleLanguageResponse($chatId, $text)
    {
        app('buttonSelectLanguage')->handleUserResponse($chatId, $text);
        app('requestShareContact')->askContactInfo($chatId);
        return response()->json(['message' => 'Language preference updated.']);
    }
}
