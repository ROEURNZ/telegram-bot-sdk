<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_working_hour', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->decimal('total_hours', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_working_hour');
    }
};
