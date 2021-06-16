<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomLayoutsTable extends Migration
{
    public function up()
    {
        Schema::create('room_layouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('room_id');
            $table->string('room_no');
            $table->string('floor');
            $table->bigInteger('rank');
            $table->bigInteger('maintain')->default(0);
            $table->tinyInteger('trash')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_layouts');
    }
}
