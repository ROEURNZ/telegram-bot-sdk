<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;

use App\Services\TelegramCommandService;

class TelegramController extends Controller
{
    public function setCommandMenu(TelegramCommandService $telegramService)
    {
        return $telegramService->setCommandMenu();
    }
}
