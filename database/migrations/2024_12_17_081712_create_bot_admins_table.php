<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bot_admin', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('admin_name', 100)->nullable();
            $table->string('step', 100)->nullable();
            $table->text('temp')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('bot_admin');
    }
};
