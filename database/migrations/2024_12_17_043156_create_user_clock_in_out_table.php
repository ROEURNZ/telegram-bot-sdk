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
            $table->string('clock_in_day', 10)->nullable(); // clock_in_day column (varchar)
            $table->string('clock_in_location_status')->nullable(); // clock_in_location_status column (varchar)
            $table->string('clock_in_lat', 100)->nullable(); // clock_in_lat column (varchar)
            $table->string('clock_in_lon', 100)->nullable(); // clock_in_lon column (varchar)
            $table->integer('clock_in_location_msg_id')->nullable(); // clock_in_location_msg_id column (int)
            $table->string('clock_in_distance', 55)->nullable(); // clock_in_distance column (varchar)
            $table->string('clock_in_time_status')->nullable(); // clock_in_time_status column (varchar)
            $table->time('clock_in_time')->nullable(); // clock_in_time column (time)
            // $table->time('work_start_time')->nullable(); // work_start_time column (time)
            $table->string('clock_in_selfie_msg_id', 100)->nullable(); // clock_in_selfie_msg_id column (varchar)
            $table->string('is_clock_in', 100)->nullable(); // is_clock_in column (varchar)
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
