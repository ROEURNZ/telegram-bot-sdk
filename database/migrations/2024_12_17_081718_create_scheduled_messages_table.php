<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scheduled_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->text('destination');
            $table->string('media_type');
            $table->string('media');
            $table->integer('runtime');
            $table->datetime('last_run');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_messages');
    }
};
