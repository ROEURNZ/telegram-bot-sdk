<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
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

            // For web
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

/** stage 1
 * When User clicks start to store the data from telegram bot are:
 * chat_id
 * telegram_id (or call user telegram id)
 * message_id (telegram message id)
 * first_name
 * last_name
 * username (telegram username that's usually start with @ tag)
 * language (telegram language)
 * date (The time that user register, automatically)
 *
 *
 * stage 2
 * If user send his contact to store his contact into database
 *
 */
