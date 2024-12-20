<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();

            // For Telegram bot
            $table->unsignedBigInteger('chat_id')->unique(); // chat.id for user's chat id
            $table->unsignedBigInteger('telegram_id')->unique(); // from.id telegram user id
            $table->integer('message_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('phone_number')->nullable();
            $table->tinyInteger('permission')->default(1)->nullable();
            $table->enum('language', ['en', 'kh'])->default('en');
            $table->timestamp('date')->nullable(); 

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
