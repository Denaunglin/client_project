<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode');
            $table->string('item_id');
            $table->string('unit');
            $table->bigInteger('minimun_qty')->default(0);
            $table->integer('stock_in_hand')->default(0);
            $table->integer('to_re_order')->default(0);
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
        Schema::dropIfExists('order_lists');
    }
}
