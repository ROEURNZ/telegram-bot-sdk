<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bot_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('time_tolerance')->nullable();
            $table->decimal('location_tolerance', 10, 3);
            $table->tinyInteger('userbreak_req_step');
            $table->tinyInteger('clockuser_req_step');
            $table->tinyInteger('dead_man_feature')->default(0);
            $table->integer('dead_man_task_time')->default(30);
            $table->text('welcome_msg');
            $table->string('welcome_img', 100);
            $table->string('company_email', 50);
            $table->string('company_phone', 30);
            $table->tinyInteger('module_visit')->default(0);
            $table->tinyInteger('module_alert')->default(0);
            $table->tinyInteger('module_break')->default(0);
            $table->integer('clockout_reminder_interval');
            $table->integer('clockout_reminder_timeout');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bot_settings');
    }
};
