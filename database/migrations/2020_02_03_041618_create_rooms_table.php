<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('room_type');
            $table->bigInteger('bed_type');
            $table->integer('adult_qty')->default(0);
            $table->string('price');
            $table->string('foreign_price');
            $table->integer('extra_bed_qty')->nullable()->default(0);
            $table->integer('extra_bed_mm_price')->nullable()->default(0);
            $table->integer('extra_bed_foreign_price')->nullable()->default(0);

            $table->integer('early_checkin_mm')->nullable()->default(0);
            $table->integer('early_checkin_foreign')->nullable()->default(0);

            $table->integer('late_checkout_mm')->nullable()->default(0);
            $table->integer('late_checkout_foreign')->nullable()->default(0);
             $table->integer('room_qty')->default(0);
            $table->longText('description');
            $table->string('image');
            $table->text('facilities')->nullable();
            $table->tinyInteger('trash')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
