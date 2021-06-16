<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\HelperFunction;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\ExtraInvoice;
use App\Models\Invoice;
use App\Models\OneSignalSubscriber;
use App\Models\Tax;
use App\Models\User;
use App\Notifications\NewInvoiceNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Notification;
use PDF;
use Storage;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_invoice')) {
            abort(404);
        }

        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $invoices = Invoice::anyTrash($request->trash)->orderBy('id', 'desc')->with('booking');

            if ($daterange) {
                $invoices = $invoices->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            return Datatables::of($invoices)
                ->addColumn('action', function ($invoice) use ($request) {
                    $restore_btn = '';
                    $detail_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('view_invoice')) {
                        $detail_btn = '<a class="detail text text-primary" href="' . route('admin.invoices.detail', ['invoice' => $invoice->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_invoice')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $invoice->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $invoice->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $invoice->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${restore_btn} ${trash_or_delete_btn}";
                })

                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.admin.invoices.index');
    }

    public function destroy(Invoice $invoice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_invoice')) {
            abort(404);
        }
        $file = $invoice->invoice_file;
        Storage::delete('uploads/pdf/' . $file);
        $invoice->delete();

        activity()
            ->performedOn($invoice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('Invoice is deleted');

        return ResponseHelper::success();
    }

    public function trash(Invoice $invoice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_invoice')) {
            abort(404);
        }
        $invoice->trash();
        activity()
            ->performedOn($invoice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('Invoice is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(Invoice $invoice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_invoice')) {
            abort(404);
        }
        $invoice->restore();

        activity()
            ->performedOn($invoice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('Invoice is restored from trash');

        return ResponseHelper::success();
    }

    public function detail($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_invoice')) {
            abort(404);
        }

        $invoice = Invoice::with('booking')->where('id', $id)->first();
        $booking = Booking::where('id', $invoice->booking_id)->first();
        $nationality = config('app.nationality');
        $pay_method = config('app.pay_method');
        if ($booking->nationality == 1) {
            $sign1 = "";
            $sign2 = "MMK";
        } else {
            $sign1 = "$";
            $sign2 = "";
        }

        return view('backend.admin.invoices.detail', compact('invoice', 'booking', 'nationality', 'pay_method', 'sign1', 'sign2'));
    }

    public function printPdf($id)
    {

        $app_id = config('app.signal_app_id');
        $booking = Booking::where('id', $id)->first();
        $subscribers = OneSignalSubscriber::where('user_id', $booking->client_user)->get();
        $subscriber = $subscribers;
        $subscriber_count = $subscribers->count();
        $invoice_pdf = new Invoice();
        $invoice_pdf->booking_id = $booking->id;
        $invoice_pdf->save();

        activity()
            ->performedOn($invoice_pdf)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('New Invoice is created');

        $nights = Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out));
        $date = Carbon::now();

        if ($booking->early_late) {
            $early_late_check = unserialize($booking->early_late);
        }

        $roomno = 0;
        $roomtype = 0;
        $bedtype = 0;
        if ($booking->roomschedule) {
            $roomtype = $booking->room->roomtype->name;
            $roomno = $booking->roomschedule->roomlayout->room_no;
            $bedtype = $booking->room->bedtype->name;
        }
        if ($booking->nationality == 1) {
            $sign1 = " ";
            $sign2 = "MMK";
        } else {
            $sign1 = "$";
            $sign2 = " ";
        }

        $room_qty = $booking->room_qty;
        $booking_no = $booking->booking_number;
        $today = $date->toFormattedDateString();
        $user = $booking->client_user;
        $user = User::where('id', $user)->first();

        $commission = $booking->commission ? $booking->commission : 0;
        $commission_percentage = $booking->commission_percentage ? $booking->commission_percentage : 0;

        $commercialtax = Tax::where('id', 1)->first();
        $commercial_tax = $commercialtax->amount;
        $servicetax = Tax::where('id', 2)->first();
        $service_tax = $servicetax->amount;
        $discount = $booking->discount_price ? $booking->price - $booking->discount_price : '0';

        if ($booking->early_late) {

            if ($booking->early_check_in) {
                $el_check = 1;
                $early_late = $booking->early_check_in;
            } elseif ($booking->late_check_out) {
                $el_check = 2;
                $early_late = $booking->late_check_out;
            } elseif ($booking->both_check) {
                $el_check = 3;
                $early_late = $booking->both_check;
            } else {
                $early_late = 0;
                $el_check = 0;
            }

        } else {
            $early_late = 0;
            $el_check = 0;
        }

        if ($booking->other_services) {
            $other_charges_total = $booking->other_charges_total;
            $other_services = 1;
        } else {
            $other_services = 0;
            $other_charges_total = 0;
        }

        $invoice_number = str_pad($invoice_pdf->id, 6, '0', STR_PAD_LEFT);
        $data = [

            'bedtype' => $bedtype,
            'roomtype' => $roomtype,
            'roomno' => $roomno,
            'today_date' => $today,
            'client_name' => $booking->name,
            'client_email' => $booking->email,
            'invoice_no' => $invoice_number,
            'title' => 'Booking Invoice',
            'heading1' => 'Booking',
            'heading2' => 'Invoice',
            'date_checkin' => $booking->check_in,
            'date_checkout' => $booking->check_out,
            'price' => $booking->price,
            'discounts' => $booking->discount_price,
            'extra_bed_qty' => $booking->extra_bed_qty,
            'extra_bed_total' => $booking->extra_bed_total,
            'early_late' => $early_late,
            'service_charges' => $booking->service_tax,
            'total' => $booking->total,
            'el_check' => $el_check,
            'room_qty' => $room_qty,
            'other_charges_total' => $other_charges_total,
            'commercialtax' => $booking->commercial_tax,
            'grand_total' => $booking->grand_total,
            'nationality' => $booking->nationality,
            'other_services' => $other_services,
            'early_late_data' => $booking->early_late,
            'commission' => $commission,
            'commission_percentage' => $commission_percentage,
            'nights' => $nights,
            'booking_no' => $booking_no,
            'discount' => number_format($discount, 2, '.', ''),
            'commercial_tax' => $commercial_tax,
            'service_tax' => $service_tax,
            'sign1' => $sign1,
            'sign2' => $sign2,
        ];

        if ($user) {
            $details = [
                'title' => 'Your invoice are ready to download ! - Apex Hotel  ',
                'detail' => 'We are contacting you in regard to a new invoice that has been created for your booking room.
                You can easily check your invoice by clicking the View Detail button that will redirect to your booking. Please, check your invoice details and download your invoices',
                'link' => url(''),
                'web_link' => config('app.base_url') . '/booking/history/detail/' . $booking->booking_number,
                'deep_link' => config('deep_link.host') . config('deep_link.types.1') . '/' . $booking->booking_number,
                'order_id' => $invoice_number,
            ];

            Notification::send($user, new NewInvoiceNotification($details));
        }

        $pdf = PDF::loadView('frontend.client.pdf_view', $data);
        $pdf_name = uniqid() . '_' . time() . '_' . $booking->booking_number . '.pdf';
        $invoice_pdf->invoice_no = $invoice_number;
        $invoice_pdf->invoice_file = $pdf_name;
        $invoice_pdf->update();

        Storage::put('uploads/pdf/' . $pdf_name, $pdf->output());
        $pdf->download('apex_hotel_invoice.pdf');

        if ($subscriber) {

            $latested_notification_id = $user ? $user->notifications->last()->id : '';

            $details = [
                'title' => 'Your invoices are ready to download ! - Apex Hotel',
                'detail' => '"A new invoice that has been created for your booking room ".',
                'link' => url(''),
                'web_link' => config('app.base_url') . '/customer/notification/' . $latested_notification_id . '/mark-as-read',
                'deep_link' => config('deep_link.host') . config('deep_link.types.1') . '/booking_id=' . $booking->booking_number . '&noti_id=' . $latested_notification_id,
                'order_id' => $invoice_number,
            ];

            if ($subscriber_count == 1) {
                $signal_id = $subscriber->first()->signal_id;
                $response = HelperFunction::sendMessage($app_id, $signal_id, $details);

            } else {
                foreach ($subscriber as $data) {
                    $signal_id = $data->signal_id;
                    $response = HelperFunction::sendMessage($app_id, $signal_id, $details);
                }
            }
        } else {
            return ResponseHelper::failedMessage('User not subscribe notificaiton !');
        }

        return redirect()->back();
    }

    public function printExtraPdf($id)
    {
        $app_id = config('app.signal_app_id');
        $booking = Booking::where('id', $id)->first();
        $subscribers = OneSignalSubscriber::where('user_id', $booking->client_user)->get();
        $subscriber = $subscribers;
        $subscriber_count = $subscribers->count();

        $invoice_pdf = new ExtraInvoice();
        $invoice_pdf->booking_id = $booking->id;
        $invoice_pdf->save();

        activity()
            ->performedOn($invoice_pdf)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('New Extra Invoice is created');

        $accounttype = AccountType::where('trash', '0')->get();
        $commercialtax = Tax::where('id', 1)->first();
        $commercial_tax = $commercialtax->amount;
        $grandtotal = $booking->other_charges_total * ($commercial_tax / 100);

        $roomno = 0;
        $roomtype = 0;
        $bedtype = 0;
        if ($booking->roomschedule) {
            $roomtype = $booking->room->roomtype->name;
            $roomno = $booking->roomschedule->roomlayout->room_no;
            $bedtype = $booking->room->bedtype->name;
        }
        if ($booking->nationality == 1) {
            $sign1 = " ";
            $sign2 = "MMK";
        } else {
            $sign1 = "$";
            $sign2 = " ";
        }

        $date = Carbon::now();
        $otherservices = unserialize($booking->other_services);
        $today = $date->toFormattedDateString();
        $booking_no = $booking->booking_number;
        $user = $booking->client_user;
        $user = User::where('id', $user)->first();

        if ($user) {
            $accounttype = AccountType::where('id', $user->account_type)->first();
        }
        $invoice_number = str_pad($invoice_pdf->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'grandtotal' => number_format($grandtotal, 2, '.', ''),
            'commercial_tax' => number_format($commercial_tax, 2, '.', ''),
            'roomno' => $roomno,
            'roomtype' => $roomtype,
            'bedtype' => $bedtype,
            'invoice_no' => $invoice_number,
            'today_date' => $today,
            'client_name' => $booking->name,
            'client_email' => $booking->email,
            'title' => 'Booking Extra Invoice',
            'heading1' => 'Booking',
            'heading2' => 'Extra Invoice',
            'booking_no' => $booking_no,
            'booking' => $booking,
            'nationality' => $booking->nationality,
            'otherservices' => $otherservices,
            'other_charges_total' => $booking->other_charges_total,
            'sign1' => $sign1,
            'sign2' => $sign2,
        ];

        if ($user) {

            $details = [
                'title' => 'Your extra invoices are ready to download ! - Apex Hotel',
                'detail' => 'We are contacting you in regard to a new extra invoice that has been created for your booking room .
                You can easily check your extra invoice by clicking the View Detail button that will redirect to your booking. Please, check your invoice details and download your invoices ',
                'link' => url(''),
                'web_link' => config('app.base_url') . '/booking/history/detail/' . $booking->booking_number,
                'deep_link' => config('deep_link.host') . config('deep_link.types.1') . '/' . $booking->booking_number,
                'order_id' => $invoice_number,
            ];
            Notification::send($user, new NewInvoiceNotification($details));
        }

        $pdf = PDF::loadView('backend.admin.invoices.pdf_view', $data);
        $pdf_name = uniqid() . '_' . time() . '_' . $booking->booking_number . '.pdf';

        $invoice_pdf->invoice_no = $invoice_number;
        $invoice_pdf->invoice_file = $pdf_name;
        $invoice_pdf->update();

        Storage::put('uploads/pdf/' . $pdf_name, $pdf->output());
        $pdf->download('apex_hotel_extra_invoice.pdf');

        if ($subscriber) {
            $latested_notification_id = $user ? $user->notifications->last()->id : '';
            $details = [
                'title' => 'Your extra invoices are ready to download ! - Apex Hotel',
                'detail' => '"A new extra invoice that has been created for your booking room ".',
                'link' => url(''),
                'web_link' => config('app.base_url') . '/customer/notification/' . $latested_notification_id . '/mark-as-read',
                'deep_link' => config('deep_link.host') . config('deep_link.types.1') . '/booking_id=' . $booking->booking_number . '&noti_id=' . $latested_notification_id,
                'order_id' => $invoice_number,
            ];

            if ($subscriber_count == 1) {
                $signal_id = $subscriber->first()->signal_id;
                $response = HelperFunction::sendMessage($app_id, $signal_id, $details);
            } else {
                foreach ($subscriber as $data) {
                    $signal_id = $data->signal_id;
                    $response = HelperFunction::sendMessage($app_id, $signal_id, $details);
                }

            }
        }

        return redirect()->back();

    }
}
