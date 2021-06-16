<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarlyLatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('early_lates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_account_id');
            $table->string('early_checkin_mm');
            $table->string('early_checkin_foreign');
            $table->string('late_checkout_mm');
            $table->string('late_checkout_foreign');
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
        Schema::dropIfExists('early_lates');
    }
}
