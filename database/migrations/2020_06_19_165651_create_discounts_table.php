<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_account_id');
            $table->string('item_id');
            $table->string('discount_percentage_mm')->nullable()->default(0);
            $table->string('discount_amount_mm')->nullable()->default(0);
            $table->string('addon_percentage_mm')->nullable()->default(0);
            $table->string('addon_amount_mm')->nullable()->default(0);
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
        Schema::dropIfExists('discounts');
    }
}
