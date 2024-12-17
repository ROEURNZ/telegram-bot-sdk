<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bot_cron', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('cron_file', 50);
            $table->string('cron_command', 200);
            $table->text('cron_config');
            $table->tinyInteger('cron_active')->default(1);
            $table->dateTime('last_run');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bot_cron');
    }
};
