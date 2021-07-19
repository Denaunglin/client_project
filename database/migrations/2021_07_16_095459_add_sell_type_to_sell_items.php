<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellTypeToSellItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sell_items', function (Blueprint $table) {
            $table->tinyInteger('sell_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sell_items', function (Blueprint $table) {
            $table->dropColumn->tinyInteger('sell_type')->default(0);

        });
    }
}
