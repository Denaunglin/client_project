<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('room_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('room_no');
            $table->string('guest');
            $table->string('room_qty');
            $table->string('extra_bed_qty');
            $table->string('nationality');
            $table->string('client_user')->nullable();
            $table->bigInteger('room_id');
            $table->bigInteger('booking_id')->nullable();
            $table->string('check_in');
            $table->string('check_out');
            $table->tinyInteger('status')->comment('1 => taken, 2 => checkin, 3 => checkout (no cleaning), 4 => cancel, 5 => clean');
            $table->tinyInteger('trash')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_schedules');
    }
}
