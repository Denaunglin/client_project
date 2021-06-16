<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_calendars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_id');
            $table->string('room_id');
            $table->string('room_qty');
            $table->string('check_in');
            $table->string('check_out');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_calendars');
    }
}
