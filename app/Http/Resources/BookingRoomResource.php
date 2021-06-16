<?php

namespace App\Http\Resources;

use App\Http\Resources\PayslipResource;
use App\Models\ExtraInvoice;
use App\Models\Invoice;
use App\Models\Payslip;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $commercial_percentage = 0;
        $service_percentage = 0;
        $tax = Tax::all();
        $status_config = config('app.status');
        $status_msg_config = config('app.status_msg');
        $status_text = $status_config[$this->status];
        $status_msg = $status_msg_config[$this->status];

        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        $commission = 0;
        $commission_percentage = 0;
        $invoice_total = 0;
        $otherservice_grandtotal = 0;
        $otherservice_commercial_tax = 0;
        $deposite = $this->deposite ? $this->deposite : 0;
        $nights = Carbon::parse($this->check_out)->diffInDays(Carbon::parse($this->check_in));

        if ($this->commission) {
            $commission_percentage = $this->commission_percentage;
            $commission = $this->commission;
            $invoice_total = $this->grand_total - $commission;
        }
        if ($this->other_services) {
            $otherservicesdata = unserialize($this->other_services);
            $otherservice_commercial_tax = $this->other_charges_total * ($commercial_percentage / 100);
            $otherservice_grandtotal = $this->other_charges_total + $otherservice_commercial_tax;
        } else {
            $otherservicesdata = [];
        }
        $room = $this->room ? $this->room : null;
        if ($room) {
            $price = $this->nationality == 1 ? $room->price : $room->foreign_price;

        }

        $invoice_data = Invoice::where('trash', '0')->where('booking_id', $this->id)->get();
        if ($invoice_data) {
            $invoice = $invoice_data->last();
            $invoice_download = $invoice ? $invoice->pdf_path() : null;
        }

        $extra_invoice_data = ExtraInvoice::where('trash', '0')->where('booking_id', $this->id)->get();
        if ($extra_invoice_data) {
            $extra_invoice = $extra_invoice_data->last();
            $extra_invoice_download = $extra_invoice ? $extra_invoice->pdf_path() : null;
        }

        $balance = $this->grand_total - $this->commission;

        $image = $this->room ? $this->room->image_path() : null;
        $payslip_image = $this->image_path() ? $this->image_path() : null;
        if ($this->status == 0) {
            $color_code = "007bff";
        } elseif ($this->status == 1) {
            $color_code = "28a745";
        } elseif ($this->status == 2) {
            $color_code = "dc3545";
        } elseif ($this->status == 3) {
            $color_code = "28a745";
        }

        $payslip = Payslip::where('booking_no', $this->booking_number)->orderBy('id', 'desc');
        $payslips = $payslip->paginate(10);
        $payslipdata = PayslipResource::collection($payslips);

        $rooms = $this->room ? $this->room : null;
        $room_data = new BookingDetailsRoomResource($rooms);

        $cancel_status = 0;
        if ($this->payment_status == 1) {
            $cancel_status = 1;
        } elseif ($this->cancellation) {
            $cancel_status = 1;
        } else {
            $cancel_status = 0;
        }

        if ($price > $this->discount_price) {
            $discount_price = $this->discount_price;
        } elseif ($price = $this->discount_price) {
            $discount_price = 0;
        }

        return [
            'booking_number' => $this->booking_number,
            'status' => $this->status,
            'status_text' => $status_text,
            'cancel_status' => $cancel_status,
            'status_msg' => $status_msg,
            'status_color_code' => $color_code,

            'room_type' => $room->roomtype->name,
            'bed_type' => $room->bedtype->name,
            'room_qty' => intval($this->room_qty),
            'guest_qty' => intval($this->guest),

            'room_price' => intval($price),
            'room_discount_price' => intval($discount_price),

            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nrc_passport' => $this->nrc_passport,
            'nationality' => $this->nationality,

            'pay_method' => intval($this->pay_method),
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'deposite' => intval($deposite),
            'otherservicesdata' => $otherservicesdata,
            'other_charges_total' => intval($this->other_charges_total),

            'night' => $nights,
            'extra_bed_qty' => intval($this->extra_bed_qty),
            'extra_bed_total' => intval($this->extra_bed_total),
            'early_check_in' => intval($this->early_check_in),
            'late_check_out' => intval($this->late_check_out),
            'both_check' => intval($this->both_check),

            'service_tax' => intval($this->service_tax),
            'service_percentage' => intval($service_percentage),
            'total' => floatval($this->total),
            'commercial_tax' => floatval($this->commercial_tax),
            'commercial_percentage' => intval($commercial_percentage),

            'commission' => intval($commission),
            'commission_percentage' => intval($this->commission_percentage),

            'grand_total' => floatval($this->grand_total),
            'balance' => $balance,

            'invoice_total' => $invoice_total,
            'otherservice_commercial_tax' => $otherservice_commercial_tax,
            'otherservice_grandtotal' => $otherservice_grandtotal,

            'image' => $image,
            'invoice_download_link' => $invoice_download,
            'extra_invoice_download_link' => $extra_invoice_download,
            'payslipdata' => $payslipdata,
            'room_data' => $room_data,

        ];

    }
}
