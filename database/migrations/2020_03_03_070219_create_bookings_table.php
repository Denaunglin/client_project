<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
         
            $table->string('booking_number')->nullable();
            $table->bigInteger('room_id');
            $table->bigInteger('client_user')->nullable();
            $table->string('member_type')->nullabel();
            $table->string('commission')->nullabel();
            $table->string('commission_percentage')->nullabel();
            $table->string('both_check')->nullabel();
            $table->tinyInteger('status')->default(0);
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('nrc_passport');
            $table->longText('message')->nullable();
            $table->string('price')->nullable();
            $table->string('discount_price')->nullable();
            $table->tinyInteger('nationality');
            $table->string('total')->nullable();
            $table->string('commercial_tax')->nullable();
            $table->string('service_tax')->nullable();
            $table->string('grand_total')->nullable();
            $table->string('room_qty')->nullable();
            $table->string('extra_bed_qty')->nullabel();
            $table->string('early_late')->nullable()->default(0);;
            $table->string('early_check_in')->nullable()->default(0);
            $table->string('late_check_out')->nullable()->default(0);
            $table->string('early_checkin_time')->nullable()->default(0);
            $table->string('late_checkout_time')->nullable()->default(0);
            $table->string('extra_bed_total')->nullable();
            $table->longText('other_services')->nullabel();
            $table->string('guest')->nullable();
            $table->string('credit_type')->nullable();
            $table->string('credit_no')->nullable();
            $table->string('expire_month')->nullable();
            $table->string('expire_year')->nullable();
            $table->string('pay_method');
            $table->text('check_in');
            $table->text('check_out');
            $table->string('payslip_id')->nullable();
            $table->string('payslip_image')->nullable();
            $table->string('cancellation')->nullable()->default(0);
            $table->string('cancellation_remark')->nullable();
            $table->tinyInteger('trash')->default(0);
            $table->timestamps();
        }); 
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
