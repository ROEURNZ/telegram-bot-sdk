<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scheduled_messages_time', function (Blueprint $table) {
            $table->id();
            $table->integer('message_id');
            $table->string('day', 10);
            $table->string('time', 10);
            $table->tinyInteger('is_run')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_messages_time');
    }
};
