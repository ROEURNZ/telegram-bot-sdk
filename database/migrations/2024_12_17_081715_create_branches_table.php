<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch', function (Blueprint $table) {
            $table->id('branch_id');
            $table->string('branch_name', 100)->nullable();
            $table->string('branch_lat', 100)->nullable();
            $table->string('branch_lon', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch');
    }
};
