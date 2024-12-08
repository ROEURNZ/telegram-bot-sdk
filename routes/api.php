<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use Telegram\Bot\Laravel\Facades\Telegram;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Telegram webhooks
Route::prefix('telegram/webhooks')->group(function () {
    // Route::post('inbound',function(Request $request){
    //     Log::info($request->all());
    // });

    Route::post('inbound', [TelegramController::class, 'inbound'])->name('telegram.inbound');

    // Route::post('/webhooks', function () {
    //     $update = Telegram::commandsHandler(true);
    //     return 'OK';
    // });
});
