<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('buyer_id');
            $table->bigInteger('item_id');
            $table->integer('origin_amount');
            $table->integer('credit_amount');
            $table->integer('paid_amount');
            $table->integer('remain_amount');
            $table->itmestamp('paid_date');
            $table->string('paid_times');
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
        Schema::dropIfExists('credits');
    }
}
