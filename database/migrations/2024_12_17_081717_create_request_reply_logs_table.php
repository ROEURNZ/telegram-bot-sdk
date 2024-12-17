<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('request_reply_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->notNull();
            $table->text('user_request');
            $table->text('bot_reply');
            $table->text('api_request_url');
            $table->text('api_response');
            $table->text('wrong_reply_user_stat');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_reply_logs');
    }
};
