<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Telegram\TelegramController;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Services\TelegramCommandService;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Telegram webhooks
Route::prefix('telegram/webhooks')->group(function () {
    // Route::post('inbound',function(Request $request){
    //     Log::info($request->all());
    // });

    Route::post('inbound', [TelegramController::class, 'inbound'])->name('telegram.inbound');

});


Route::get('/set-telegram-menu', function (TelegramCommandService $telegramService) {
    return $telegramService->setCommandMenu();
});

