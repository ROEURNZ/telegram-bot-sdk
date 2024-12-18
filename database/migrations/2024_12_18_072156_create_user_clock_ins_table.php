<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserClockInsTable extends Migration
{
    public function up()
    {
        Schema::create('user_clock_ins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('clock_in_day', 10)->nullable();
            $table->string('clock_in_location_status', 255)->nullable();
            $table->string('clock_in_lat', 100)->nullable();
            $table->string('clock_in_lon', 100)->nullable();
            $table->integer('clock_in_location_msg_id')->nullable();
            $table->string('clock_in_distance', 55)->nullable();
            $table->string('clock_in_time_status', 255)->nullable();
            $table->time('clock_in_time')->nullable();
            $table->time('work_start_time')->nullable();
            $table->string('clock_in_selfie_msg_id', 100)->nullable();
            $table->string('is_clock_in', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_clock_ins');
    }
}
