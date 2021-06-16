<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpeningPriceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opening_price_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('barcode');
            $table->string('item_name');
            $table->string('unit');
            $table->string('item_group');
            $table->string('item_sub_group');
            $table->integer('opening_qty');
            $table->integer('original_price');
            $table->integer('net_price');
            $table->tinyInteger('trash')->default(0);    
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
        Schema::dropIfExists('opening_price_items');
    }
}
