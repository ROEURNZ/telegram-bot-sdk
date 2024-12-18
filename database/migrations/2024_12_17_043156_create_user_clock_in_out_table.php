<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserClockInOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_clock_in_out', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (id)
            $table->bigInteger('user_id'); // user_id column (bigint)
            $table->string('clock_day', 10)->nullable(); // clock_day column (varchar)
            $table->string('clock_location_status')->nullable(); // clock_location_status column (varchar)
            $table->string('clock_lat', 100)->nullable(); // clock_lat column (varchar)
            $table->string('clock_lon', 100)->nullable(); // clock_lon column (varchar)
            $table->integer('clock_location_msg_id')->nullable(); // clock_location_msg_id column (int)
            $table->string('clock_distance', 55)->nullable(); // clock_distance column (varchar)
            $table->string('clock_time_status')->nullable(); // clock_time_status column (varchar)
            $table->time('clock_time')->nullable(); // clock_time column (time)
            $table->time('work_start_time')->nullable(); // work_start_time column (time)
            $table->string('clock_selfie_msg_id', 100)->nullable(); // clock_selfie_msg_id column (varchar)
            $table->string('clock_status', 100)->nullable(); // is_clock_in column (varchar)
            $table->timestamps(0); // Nullable timestamps (created_at and updated_at)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_clock_in_out');
    }
}
