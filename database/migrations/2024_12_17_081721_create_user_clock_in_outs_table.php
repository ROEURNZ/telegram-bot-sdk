<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_clock_in_out', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('clock_type', 10);
            $table->time('clock_time');
            $table->string('location_status', 10);
            $table->string('location_lat', 20);
            $table->string('location_lon', 20);
            $table->integer('location_msg_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_clock_in_out');
    }
};
