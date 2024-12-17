<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_break', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('break_day');
            $table->time('break_time');
            $table->string('location_status');
            $table->string('location_lat');
            $table->string('location_lon');
            $table->integer('location_msg_id');
            $table->string('location_distance');
            $table->string('selfie_msg_id');
            $table->string('break_action');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_break');
    }
};
