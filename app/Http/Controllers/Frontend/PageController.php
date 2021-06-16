<?php

namespace App\Http\Controllers\Frontend;

use App\Helper\FontConvert;
use App\Helper\SpamFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMailRequest;
use App\Http\Requests\RetrieveBookingValidate;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\ExtraInvoice;
use App\Models\Invoice;
use App\Models\Messages;
use App\Models\OneSignalSubscriber;
use App\Models\Payslip;
use App\Models\Rooms;
use App\Models\SliderUpload;
use App\Models\Tax;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PageController extends Controller
{
    public function index()
    {
        $app = config('app.facilities');
        $slider = SliderUpload::where('trash', 0)->get();
        $rooms = Rooms::with('discount_types')->orderBy('id', 'desc')->where('trash', 0)->paginate(3);
        $room_limit = 0;

        if (Auth::user()) {
            if (Auth::user()->accounttype->booking_limit = 1) {
                $room_limit = 1;
            }
        }

        return view('frontend.index', compact('slider', 'room_limit', 'rooms', 'app'));

    }

    public function aboutus()
    {
        return view('frontend.aboutus');
    }

    public function contactus()
    {
        return view('frontend.contact_us');
    }

    public function contactMail(ContactMailRequest $request)
    {
        if (SpamFilter::result($request->name) || SpamFilter::result($request->email) || SpamFilter::result($request->phone) || SpamFilter::result($request->message)) {
            return back()->with('success', 'Thanks so much for your request.We check your request frequently and will try our best to respond to your request.');
        }
        $mail = new Messages();
        $mail->name = FontConvert::zg2uni($request['name']);
        $mail->email = $request['email'];
        $mail->phone = $request['phone'];
        $mail->message = FontConvert::zg2uni($request['message']);
        $mail->save();

        return redirect()->back()->with(['success' => "Successfully sent your message"]);
    }

    public function bookingDetail(Request $request)
    {

        $booking = Booking::where('id', $request->id)->firstOrFail();

        return view('frontend.booking_detail', compact('booking'));
    }

    public function bookingRetrieve()
    {
        $roomtype = Rooms::where('trash', 0)->get();

        return view('frontend.booking_retrieve', compact('roomtype'));
    }

    public function bookingRetrieveDetail(RetrieveBookingValidate $request)
    {

        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        $pay_method = config('app.pay_method');
        $status_msg = config('app.status_msg');
        $checkin_checkout = explode(' - ', $request->checkin_checkout);
        $q1 = $request->room_type;
        $q2 = $checkin_checkout[0];
        $q3 = $checkin_checkout[1];
        $q4 = $request->phone;

        $booking = Booking::with('room')->where('booking_number', $request->booking_no)->where('room_id', $q1)->where('check_in', $q2)->where('check_out', $q3)->where('phone', $q4)->first();

        if ($booking) {
            $invoice_data = Invoice::where('trash', '0')->where('booking_id', $booking->id)->get();
            if ($invoice_data) {
                $invoice = $invoice_data->last();
            }
            $extra_invoice_data = ExtraInvoice::where('trash', '0')->where('booking_id', $booking->id)->get();
            $extra_invoice = $extra_invoice_data->last();
            $commission = 0;
            $commission_percentage = 0;

            if (Auth::user()) {
                if ($booking->client_user) {
                    $user = User::where('id', $booking->client_user)->first();
                    $account = AccountType::where('id', $user->account_type)->first();
                    $commission_percentage = $booking->commission_percentage;
                    $commission = $booking->commission;
                }
            }

            if ($booking->other_services) {
                $otherservicesdata = unserialize($booking->other_services);
            } else {
                $otherservicesdata = null;
            }

            if ($booking->nationality == 1) {
                $sign1 = '';
                $sign2 = 'MMK';
            } else {
                $sign1 = '$';
                $sign2 = '';
            }
            $grandtotal = $booking->other_charges_total * ($commercial_percentage / 100);

            if ($booking) {
                $payslips = Payslip::where('booking_no', $booking->booking_number)->get();
                $payslip = $payslips->last();
            } else {
                $payslip = null;
            }

            return view('frontend.booking_detail', compact('grandtotal', 'status_msg', 'payslip', 'commercial_percentage', 'invoice', 'extra_invoice', 'commission', 'commission_percentage', 'booking', 'sign1', 'sign2', 'pay_method', 'otherservicesdata'));
        }

        return back()->withErrors(['not_found' => 'Your booking not found. Please check your booking information.'])->withInput();
    }

    public function Policies()
    {
        return view('frontend.policies');
    }

    public function termsAndConditions()
    {
        return view('frontend.terms_conditions');
    }

    public function saveNoti(Request $request)
    {
        $user = Auth::user()->id;
        $notis = OneSignalSubscriber::where('user_id', $user)->where('signal_id', $request->signal_id)->where('browser', $request->browser)->first();

        if ($notis) {
            $notis->user_id = $user;
            $notis->signal_id = $request['signal_id'];
            $notis->browser = $request['browser'];
            $notis->update();
            return redirect()->back();
        } else {
            $noti = new OneSignalSubscriber();
            $noti->user_id = $user;
            $noti->signal_id = $request['signal_id'];
            $noti->browser = $request['browser'];
            $noti->save();
            return redirect()->back();

        }

    }

    public function unsubscribeNoti(Request $request)
    {
        $user = Auth::user()->id;
        $notis = OneSignalSubscriber::where('user_id', $user)->where('signal_id', $request->signal_id)->where('browser', $request->browser)->first();
        $notis->delete();
    }

    public function ssdPayslip(Request $request)
    {
        if ($request->ajax()) {
            $booking = Booking::where('id', $request->id)->first();
            $payslips = Payslip::where('trash', '0')->where('booking_no', $booking->booking_number)->get();
            $config_status = config('app.status');
            return DataTables::of($payslips)
                ->addColumn('widget', function ($payslips) use ($request, $config_status) {
                    if ($payslips->status == 0) {
                        $status_color = "badge-warning";
                    } elseif ($payslips->status == 1) {
                        $status_color = "badge-success";
                    } elseif ($payslips->status == 2) {
                        $status_color = "badge-danger";
                    } elseif ($payslips->status == 3) {
                        $status_color = "badge-success";
                    }
                    return '<div class="list border-left">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1 m-2"><a style="color:#44da46;" href="/customer/payslips/' . $payslips->id . '/mark-as-read"></a></h5>
                                            <p class="mb-1 text-muted"></p>
                                        </div>
                                        <div class="d-flex w-100 justify-content-between">
                                            <img src="' . $payslips->image_path() . '" width="100%" height="30%">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="action m-2">
                                            <label>-Remark-</label>
                                            <p> ' . $payslips->remark . ' </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="action m-2">
                                            <label>-Status-</label>
                                            <p><span class=" badge ' . $status_color . ' " > ' . $config_status[$payslips->status] . '</span> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->rawColumns(['widget'])
                ->make(true);
        }
    }

}
