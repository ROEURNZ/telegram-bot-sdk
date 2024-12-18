<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserClockOutsTable extends Migration
{
    public function up()
    {
        Schema::create('user_clock_outs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('clock_out_day', 10)->nullable();
            $table->string('clock_out_location_status', 255)->nullable();
            $table->string('clock_out_lat', 100)->nullable();
            $table->string('clock_out_lon', 100)->nullable();
            $table->integer('clock_out_location_msg_id')->nullable();
            $table->string('clock_out_distance', 55)->nullable();
            $table->string('clock_out_time_status', 255)->nullable();
            $table->time('clock_out_time')->nullable();
            $table->time('work_end_time')->nullable();
            $table->string('clock_out_selfie_msg_id', 100)->nullable();
            $table->string('is_clock_out', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_clock_outs');
    }
}
