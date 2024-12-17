<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('task_name');
            $table->dateTime('task_start_time');
            $table->dateTime('task_end_time');
            $table->text('task_description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_daily_tasks');
    }
};
