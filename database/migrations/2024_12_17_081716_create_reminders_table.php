<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->notNull();
            $table->string('type');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('sent')->default(0);
            $table->integer('reply')->default(0);
            $table->string('response')->nullable();
            $table->text('reminder_msg')->nullable();
            $table->text('reminder_button')->nullable();
            $table->integer('reminder_num')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reminders');
    }
};
