<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('image')->nullable();
            $table->string('barcode');
            $table->string('name');
            $table->string('unit');
            $table->bigInteger('item_category_id');
            $table->bigInteger('item_sub_category_id')->nullable()->default(0);;
            $table->bigInteger('minimun_qty')->default(0);
            $table->integer('buying_price')->default(0);
            $table->integer('retail_price')->default(0);
            $table->integer('wholesale_price')->default(0);
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
        Schema::dropIfExists('items');
    }
}
