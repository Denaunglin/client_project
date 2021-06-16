<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarlyLateChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('early_late_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_account_id');
            $table->string('add_early_checkin_mm');
            $table->string('add_early_checkin_foreign');
            $table->string('add_late_checkout_mm');
            $table->string('add_late_checkout_foreign');
            $table->string('subtract_early_checkin_mm');
            $table->string('subtract_early_checkin_foreign');
            $table->string('subtract_late_checkout_mm');
            $table->string('subtract_late_checkout_foreign');
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
        Schema::dropIfExists('early_late_checks');
    }
}
