<?php

namespace App\Http\Controllers\Frontend\Client;

use App\Helper\FontConvert;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\CardType;
use App\Models\ExtraInvoice;
use App\Models\Invoice;
use App\Models\Payslip;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserCreditCard;
use App\Models\UserNrcPicture;
use App\Models\UserProfile;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        $status = config('app.status');
        $pay_method = config('app.pay_method');
        $option = '0';
        $user = Auth::user()->id;
        $booking = Booking::where('client_user', $user)->orderBy('id', 'desc')->paginate(3);

        $month = config('app.month');
        $present_year = Carbon::now()->format('Y');
        $year_loop = [];
        for ($i = 1; $i <= 10;) {
            $year_loop[] = $present_year++;
            $i++;
        }
        $year = collect($year_loop);
        $usercard = UserCreditCard::where('user_id', $user)->orderBy('id', 'desc')->get();
        $cardtype = CardType::where('trash', '0')->get();

        return view('frontend.client.dashboard', compact('booking', 'pay_method', 'month', 'year', 'cardtype', 'usercard', 'status', 'option'));
    }

    public function profileEdit()
    {
        $month = config('app.month');
        $present_year = Carbon::now()->format('Y');
        $year_loop = [];

        for ($i = 1; $i <= 10;) {
            $year_loop[] = $present_year++;
            $i++;
        }
        $year = collect($year_loop);
        $cardtype = CardType::where('trash', '0')->get();
        $profile = UserProfile::all();
        $user = Auth::user()->id;
        $usernrcimage = UserNrcPicture::where('user_id', $user)->get();
        $lastusernrc = $usernrcimage->last();
        $usercard = UserCreditCard::where('user_id', $user)->orderBy('id', 'desc')->paginate(2);

        return view('frontend.client.profile', compact('profile', 'cardtype', 'month', 'year', 'lastusernrc', 'usercard'));
    }

    public function AddUserCard(Request $request)
    {

        $user = Auth::user();
        if ($user) {

            $usercreditcard = new UserCreditCard();
            $usercreditcard->user_id = $user->id;
            $usercreditcard->credit_type = $request->credit_type;
            $usercreditcard->credit_no = $request->credit_no;
            $usercreditcard->expire_month = $request->expire_month;
            $usercreditcard->expire_year = $request->expire_year;
            $usercreditcard->account_name = $request->account_name;
            $usercreditcard->save();

        }
        return redirect()->back()->with(['success' => 'Successfully Add New Card !']);

    }

    public function UpdateUserCard(Request $request)
    {
        $user = Auth::user();
        $usercreditcard = UserCreditCard::find($request->id);
        $usercreditcard->user_id = $user->id;
        $usercreditcard->credit_type = $request->credit_type;
        $usercreditcard->credit_no = $request->credit_no;
        $usercreditcard->expire_month = $request->expire_month;
        $usercreditcard->expire_year = $request->expire_year;
        $usercreditcard->account_name = $request->account_name;
        $usercreditcard->update();

        return redirect()->back()->with(['success' => 'Successfully Update Card !']);

    }

    public function DeleteUserCard(Request $request)
    {
        $usercreditcard = UserCreditCard::where('id', $request->id)->first();
        if ($usercreditcard) {
            $usercreditcard->delete();
            return response("success");
        }
    }

    public function profileUpdate(Request $request)
    {

        $user = auth()->user();

        $user_nrc_pic = UserNrcPicture::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'front_pic' => '',
                'back_pic' => '',
            ]
        );

        if ($request->hasFile('front_pic')) {
            $image_file_front = $request->file('front_pic');
            $image_name_front = time() . '_' . uniqid() . '.' . $image_file_front->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name_front,
                file_get_contents($image_file_front->getRealPath())
            );

            $file_path = public_path('storage/uploads/gallery/' . $image_name_front);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);
        } else {
            $image_name_front = $user_nrc_pic->front_pic;
        }

        if ($request->hasFile('back_pic')) {
            $image_file_back = $request->file('back_pic');
            $image_name_back = time() . '_' . uniqid() . '.' . $image_file_back->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name_back,
                file_get_contents($image_file_back->getRealPath())
            );

            $file_path = public_path('storage/uploads/gallery/' . $image_name_back);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name_back = $user_nrc_pic->back_pic;
        }

        $user_nrc_pic->front_pic = $image_name_front;
        $user_nrc_pic->back_pic = $image_name_back;
        $user_nrc_pic->update();

        $user->name = FontConvert::zg2uni($request['name']);
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->nrc_passport = $request['nrc_passport'];
        $user->date_of_birth = $request['date_of_birth'];
        $user->gender = $request['gender'];
        $user->address = FontConvert::zg2uni($request['address']);
        $user->update();

        if ($request->credit_type) {
            $usercreditcard = UserCreditCard::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'credit_type' => $request->credit_type,
                    'credit_no' => $request->credit_no,
                    'expire_month' => $request->expire_month,
                    'expire_year' => $request->expire_year,
                    'account_name' => $request->account_name,
                ]
            );
        }

        $profiles = UserProfile::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'image' => '',
            ]
        );

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');
            $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );

            $file_path = public_path('storage/uploads/gallery/' . $image_name);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name = $profiles->image;
        }
        $profiles->image = $image_name;
        $profiles->update();

        return redirect()->back()->with(['success' => 'Successfully update profile !']);
    }

    public function bookingHistoryDetail(Request $request)
    {

        $booking = Booking::where('booking_number', $request->id)->first();
        $invoice_data = Invoice::where('trash', '0')->where('booking_id', $booking->id)->get();
        $invoice = $invoice_data->last();

        $extra_invoice_data = ExtraInvoice::where('trash', '0')->where('booking_id', $booking->id)->get();
        $extra_invoice = $extra_invoice_data->last();

        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        $nationality = config('app.nationality');
        $pay_method = config('app.pay_method');
        $status_msg = config('app.status_msg');
        $payslip = null;

        if ($booking) {

            $payslips = Payslip::where('booking_no', $booking->booking_number)->get();
            $payslip = $payslips->last();

            if (auth()->check()) {
                $user = Auth::user();
                $accounttype = AccountType::where('id', $user->account_type)->first();
                $commission = 0;
                $commission_percentage = 0;

                if ($booking->commission) {
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
                $sign1 = "";
                $sign2 = "MMK";
            } else {
                $sign1 = "$";
                $sign2 = "";
            }

        }

        $grandtotal = $booking->other_charges_total * ($commercial_percentage / 100);
        return view('frontend.client.booking_history_detail', compact('grandtotal', 'status_msg', 'payslip', 'commercial_percentage', 'service_percentage', 'commission_percentage', 'extra_invoice', 'invoice', 'commission', 'booking', 'nationality', 'pay_method', 'sign2', 'sign1', 'otherservicesdata'));
    }

    public function searchByOption(Request $request)
    {
        $status = config('app.status');
        $pay_method = config('app.pay_method');
        $today = Carbon::today()->toDateString();
        $user = Auth::user()->id;
        $option = $request->option;
        $token = $request->_token;

        if ($request->option == 1) {
            $booking = Booking::where('client_user', $user)->paginate(3);
            $booking->withPath(url()->current() . '?' . $token . '&' . 'option=' . $option);
            return view('frontend.client.dashboard', compact('booking', 'pay_method', 'status', 'option'));

        } elseif ($request->option == 2) {
            $booking = Booking::where('client_user', $user)->where('check_in', '<', $today)->paginate(3);
            $booking->withPath(url()->current() . '?' . $token . '&' . 'option=' . $option);
            return view('frontend.client.dashboard', compact('booking', 'pay_method', 'status', 'option'));

        } else {
            $booking = Booking::where('client_user', $user)->where('check_in', '>=', $today)->paginate(3);
            $booking->withPath(url()->current() . '?' . $token . '&' . 'option=' . $option);
            return view('frontend.client.dashboard', compact('booking', 'pay_method', 'status', 'option'));
        }

    }

    public function notificationView()
    {
        $date = Carbon::now();
        $today = $date->format('Y-m-d');
        return view('frontend.client.notifications');
    }

    public function ssd(Request $request)
    {
        if ($request->ajax()) {
            $auth_user = Auth::user();
            $notifications = $auth_user->notifications()->get();
            return DataTables::of($notifications)
                ->addColumn('widget', function ($notification) {
                    $title = $notification->data['title'] ?? '-';
                    $description = $notification->data['detail'] ?? '-';
                    $class_name = $notification->read_at ? '' : 'bg-light';

                    $markasread_icon = '';
                    if (!$notification->read_at) {
                        $markasread_icon = '<a href="#" title="Mark as read" class="markasread" data-id="' . $notification->id . '"><i class="far fa-envelope-open text-info"></i></a>';
                    }

                    $delete_icon = '<a href="#" title="Delete" class="delete" data-id="' . $notification->id . '"><i class="far fa-trash-alt text-danger"></i></a>';

                    return '<div class="list border-left ' . $class_name . '">

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1 m-2"><a style="color:#44da46;" href="/customer/notification/' . $notification->id . '/mark-as-read">' . Str::limit($title, 80) . '</a></h5>
                                            <p class="mb-1 text-muted">' . $notification->created_at->format("Y-M-d") . ' (' . Carbon::parse($notification->created_at)->diffForHumans() . ')</p>
                                        </div>
                                        <div class="d-flex w-100 justify-content-between">
                                            <p class="mb-0 m-2">' . Str::limit($description, 100) . '</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                    <div class="action m-2">' . $markasread_icon . ' ' . $delete_icon . '</div>
                                    </div>
                                </div>
                            </div>';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['widget'])
                ->make(true);
        }
    }

    public function show($notification_id)
    {
        $notifications = Auth::user()->notifications;
        $notification = $notifications->where('id', $notification_id)->first();

        if (!$notification) {
            return abort(404);
        }

        $notification->markAsRead();
        return view('frontend.client.notification_show', compact('notification'));
    }

    public function markAsRead($notification_id)
    {
        $notifications = Auth::user()->notifications;
        $notification = $notifications->where('id', $notification_id)->first();
        if ($notification) {
            $notification->markAsRead();
            return view('frontend.client.notification_detail', compact('notification'));
        }
        return redirect()->back();
    }

    public function destroy($notification_id)
    {
        $notifications = Auth::user()->notifications;
        $notification = $notifications->where('id', $notification_id)->first();
        if ($notification) {
            $notification->delete();
            return redirect()->back();
        }
        return ResponseHelper::fail();
    }

}
