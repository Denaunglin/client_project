<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode');
            $table->string('item_id');
            $table->string('customer_id');
            $table->string('unit');
            $table->bigInteger('item_category_id');
            $table->bigInteger('item_sub_category_id')->nullable()->default(0);;
            $table->bigInteger('qty')->default(0);
            $table->integer('price')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('net_price')->nullabel();
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
        Schema::dropIfExists('sell_items');
    }
}
