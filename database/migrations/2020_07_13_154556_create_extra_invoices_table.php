<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_id');
            $table->string('invoice_no')->nullabel();
            $table->text('invoice_file')->nullable();
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
        Schema::dropIfExists('extra_invoices');
    }
}
