<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_visits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->timestamp('visit_time');
            $table->string('location');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_visits');
    }
};
