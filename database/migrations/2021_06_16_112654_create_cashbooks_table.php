<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashbooks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('buying_id')->nullable();
            $table->integer('selling_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('expense_id')->nullable();
            $table->integer('credit_id')->nullable();
            $table->integer('return_id')->nullable();
            $table->integer('cashbook_income');
            $table->integer('cashbook_outgoing');
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
        Schema::dropIfExists('cashbooks');
    }
}
