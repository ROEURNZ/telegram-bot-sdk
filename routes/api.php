<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\TelegramCommandService;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\Telegram\BotController;
use App\Http\Controllers\Telegram\TelegramController;
use App\Http\Controllers\Telegram\UserContactController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Telegram webhooks
Route::prefix('telegram/webhook')->group(function () {

    Route::post('system', [BotController::class, 'system'])->name('telegram.system');
    Route::post('system1', [UserContactController::class, 'shareContactHandler'])->name('telegram.system1');
    Route::get('/system-menu', [TelegramController::class, 'setCommandMenu']);
    Route::get('/send-test-message', [TelegramController::class, 'sendTestMessage']);
    Route::get('/messages', [BotController::class, 'getMessages']);
});




// Route::get('/set-telegram-menu', function (TelegramCommandService $telegramService) {
//     return $telegramService->setCommandMenu();
// });
