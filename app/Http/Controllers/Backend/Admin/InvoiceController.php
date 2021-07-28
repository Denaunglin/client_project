<?php

namespace App\Http\Controllers\Backend\Admin;

use PDF;
use Storage;
use Notification;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Item;
use App\Models\User;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\SellItems;
use App\Models\AccountType;
use App\Models\ExtraInvoice;
use Illuminate\Http\Request;
use App\Models\Bussinessinfo;
use App\Helper\HelperFunction;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Models\OneSignalSubscriber;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Notifications\NewInvoiceNotification;

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
            $invoices = Invoice::anyTrash($request->trash)->orderBy('id', 'desc')->with('item','service');

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
                ->addColumn('invoice_about', function ($invoice) {
                    $about ='-';
                    if($invoice->item){
                        $about = $invoice->item ? $invoice->item->name : '-';
                    }
                    if($invoice->service){
                        $about = $invoice->service ? $invoice->service->service_name : '-';
                    }
                    return '<span>'.$about.'</span>';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','invoice_about'])
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

        $invoice = Invoice::with('item','service')->where('id', $id)->first();
        $item = [];
        $service =[];
        if($invoice->item){
            $item = SellItems::where('id', $invoice->item_id)->first();
        }
        if($invoice->service){
            $service = Service::where('id',$invoice->service_id)->first();
        }

        $nationality = config('app.nationality');
        $pay_method = config('app.pay_method');
      

        return view('backend.admin.invoices.detail', compact('invoice', 'item','service', 'pay_method'));
    }

    public function printSalesInvoice($id)
    {
        $sell_items = SellItems::where('id', $id)->first();
        $bussiness_info = Bussinessinfo::where('trash',0)->first();
        $invoice_pdf = new Invoice();
        $invoice_pdf->invoice_no = 0;
        $invoice_pdf->item_id = $sell_items->id;
        $invoice_pdf->service_id = null;
        $invoice_pdf->save();

        activity()
            ->performedOn($invoice_pdf)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('New Invoice is created');

        $date = Carbon::now();
        $today = $date->toFormattedDateString();
        $data = unserialize($sell_items->item_data);
        $id = [];
        $qty = [];

        $item_data = [];

        foreach($data as $test){
            $id = $test['item_id'];
            $qty =  $test['qty'] ;

            $data_item= Item::where('id',$id)->first();
            $item_name = $data_item->name;

            if($sell_items->sell_type == 0 ){
                $price = $data_item->retail_price;
            }else{
                $price = $data_item->wholesale_price;
            }

            $item_qty = $qty;
            $net_price = $item_qty * $price;

            $item_data [] = [
                    "item_name" => $item_name,
                    "item_qty" => $item_qty,
                    "price" => $price,
                    "net_price" => $net_price,
            ];
        
        }
         
        $invoice_number = str_pad($invoice_pdf->id, 6, '0', STR_PAD_LEFT);
        // dd($item_data);

        $data = [
            'today_date' => $today,
            'invoice_no' => $invoice_number,
            'title' => ' Invoice',
            'heading1' => '',
            'item_data' => $item_data,
            'heading2' => 'Invoice',
            'total_qty' => $sell_items->total_qty,
            'total_amount' => $sell_items->total_amount,
            'paid_amount' => $sell_items->paid_amount,
            'credit_amount' => $sell_items->credit_amount,
            'discount' => $sell_items->discount,
        ];
       
        $pdf = PDF::loadView('backend.admin.invoices.pdf_view', $data);
        $pdf_name = uniqid() . '_' . time() . '_' . $invoice_number . '.pdf';
        $invoice_pdf->invoice_no = $invoice_number;
        $invoice_pdf->invoice_file = $pdf_name;
        $invoice_pdf->update();

        Storage::put('uploads/pdf/' . $pdf_name, $pdf->output());
        $pdf->download('sell_invoices.pdf');

        return redirect()->back();
    }

    public function printServiceInvoice($id)
    {
        $services = Service::where('id', $id)->first();
        $bussiness_info = Bussinessinfo::where('trash',0)->first();
        $invoice_pdf = new Invoice();
        $invoice_pdf->invoice_no = 0 ;
        $invoice_pdf->item_id = null;
        $invoice_pdf->service_id =  $services->id;
        $invoice_pdf->save();

        activity()
            ->performedOn($invoice_pdf)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Invoice (Admin Panel)'])
            ->log('New Invoice is created');

        $date = Carbon::now();
        $service_name = $services ? $services->service_name : '-';
        $service_description = $services ? $services->description : '-';
        $service_charges = $services ? $services->service_charges : '-';
        $net_price = $service_charges;

        $today = $date->toFormattedDateString();
        $invoice_number = str_pad($invoice_pdf->id, 6, '0', STR_PAD_LEFT);
        $data = [

            'service_name' => $service_name,
            'service_description' => $service_description,
            'service_charges' => $service_charges,

            'shop_name' => $bussiness_info ? $bussiness_info->name : '-',
            'shop_email' => $bussiness_info ? $bussiness_info->email : '-',
            'shop_phone' => $bussiness_info ? $bussiness_info->phone : '-',
            'shop_address' => $bussiness_info ? $bussiness_info->address : '-',
            'today_date' => $today,
            // 'client_name' => $booking->name,
            // 'client_email' => $booking->email,
            'invoice_no' => $invoice_number,
            'title' => ' Invoice',
            'heading1' => '',
            'heading2' => 'Invoice',
            'net_price' => $net_price,
        ];

       
        $pdf = PDF::loadView('backend.admin.invoices.services_pdf_view', $data);
        $pdf_name = uniqid() . '_' . time() . '_' . $service_name . '.pdf';
        $invoice_pdf->invoice_no = $invoice_number;
        $invoice_pdf->invoice_file = $pdf_name;
        $invoice_pdf->update();

        Storage::put('uploads/pdf/' . $pdf_name, $pdf->output());
        $pdf->download('services_invoices.pdf');

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
        $invoice_pdf->item_id = null;
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
