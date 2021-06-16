<?php

namespace App\Http\Controllers\Frontend;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Discounts;
use App\Models\Rooms;
use App\Models\RoomType;
use App\Models\showGallery;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\DataTables;

class RoomController extends Controller
{
    public function index(Request $request)
    {

        $room_limit = 0;
        if (Auth::user()) {
            if (Auth::user()->accounttype->booking_limit = 1) {
                $room_limit = 1;
            }
        }
        $room_type = RoomType::where('trash', 0)->orderBy('id', 'desc')->get();

        return view('frontend.room_list', compact('room_limit', 'room_type'));
    }

    public function detailView($id, Request $request)
    {
        $room_limit = 0;
        $booking_limit = 0;

        if (Auth::user()) {
            if (Auth::user()->accounttype->booking_limit == 1) {
                $account = Auth::user()->id;
                $room_limit = 1;
                $today = Carbon::now()->format('Y-m-d');
                $booking_limit = Booking::where('client_user', $account)->where('check_out', '>', $today)->where('payment_status', '0')->get()->count();
            }
        }

        $nationality = $request['nationality'];
        $check_in = $request['check_in_date'];
        $check_out = $request['check_out_date'];
        $app_facilities = config('app.facilities');
        $room = Rooms::findOrFail($id);

        if ($request->nationality == 1) {
            $extra_bed_price = $room->extra_bed_mm_price;
        } else {
            $extra_bed_price = $room->extra_bed_foreign_price;
        }

        $facilities = $room->facilities ? unserialize($room->facilities) : [];
        $notavailable_facilities = [];
        $fact_id = [];

        foreach ($facilities as $fac_data) {
            $fact_id[] = $fac_data;
        }

        $facilitiesdata_key = [];
        foreach ($app_facilities as $key => $fac_data) {
            $facilitiesdata_key[] = "$key";
        }

        $collection1 = collect($facilitiesdata_key);
        $collection2 = collect($fact_id);
        $diff = $collection1->diff($collection2);

        foreach ($diff as $data) {
            $notavailable_facilities[] = $data;
        }

        $unavailable_facilities = collect($notavailable_facilities);
        $gallery = showGallery::where('rooms_id', $id)->get();
        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($room, $request->check_in_date, $request->check_out_date);

        if (Auth::user()) {

            $client_user = Auth::user();
            $accounttype = $client_user->accounttype->id;
            $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room->id)->first();
            $detailprices = ResponseHelper::roomschedulediscount($room, $nationality, $client_user, $discount_type);
            $detailprice = $detailprices['0'];
            $addon = $detailprices['1'];

        } else {

            $detailprice = ResponseHelper::sale_price($room, $nationality);
            $addon = $detailprice;
        }

        return view('frontend.detail', compact('booking_limit', 'room_limit', 'nationality', 'unavailable_facilities', 'room', 'gallery', 'app_facilities', 'facilities', 'check_in', 'check_out', 'avaliable_room_qty', 'extra_bed_price', 'detailprice', 'addon'));
    }

    public function roomListSsd(Request $request)
    {

        $data = Rooms::where('trash', 0);

        if ($request->sort) {

            if ($request->sort == 1) {
                $data = $data->orderBy('price', 'desc');
            } else if ($request->sort == 2) {
                $data = $data->orderBy('price', 'asc');
            } else {
                $data = $data->orderBy('room_type', 'desc');
            }

        } else {
            $data = $data->orderBy('room_type', 'desc');
        }

        $data = $data->get();

        if ($request->room_type) {
            $room_type = explode(',', $request->room_type);
            $data = $data->whereIn('room_type', $room_type);
        }

        if ($request->view) {
            if ($request->view == 1) {
                $data = $data;
            } else if ($request->view == 2) {
                $data = collect($data)->chunk(2);
            }
        }

        return DataTables::of($data)
            ->addColumn('widget', function ($each) use ($request) {
                if ($request->view == 2) {
                    $output = '<div class="row">';

                    foreach ($each as $data) {
                        $room_type = $data->id;
                        $roomtype = $data->roomtype ? $data->roomtype->name : '-';
                        $bedtype = $data->bedtype ? $data->bedtype->name : '-';
                        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($data, $request->check_in_date, $request->check_out_date);
                        $view_detail_msg = 'message.button.view_detail';
                        $no_availiable_msg = 'message.no_availiable';
                        $room_qty_notenough_msg = "message.room_qty_notenough_msg";
                        $sleep_msg = "message.sleeps";
                        $we_have_msg = "message.we_have";
                        $left_msg = "message.left";
                        $avaliable_room = '<p class="text-danger mb-1">' . trans($no_availiable_msg) . '</p>';

                        if ($avaliable_room_qty >= $request->room_qty) {
                            $view_detail_btn = '<a href="' . url('room/detail') . '/' . $data->id . '?check_in_date=' . $request->check_in_date . '&check_out_date=' . $request->check_out_date . '&room_qty=' . $request->room_qty . '&nationality=' . $request->nationality . '&extra_bed_qty=' . $request->extra_bed_qty . '&guest=1" class="btn btn-view-detail"> ' . trans($view_detail_msg) . ' </a>';
                        } else {
                            $view_detail_btn = '<p class="text-danger mb-1"> ' . trans($room_qty_notenough_msg) . ' </p>';
                        }

                        if ($avaliable_room_qty > 0) {
                            $avaliable_room = '<p class="text-danger mb-1"> <span class="fa fa-warning text-danger"></span> ' . trans($we_have_msg) . ' ' . $avaliable_room_qty . ' ' . trans($left_msg) . ' </p>';
                        }

                        if (Auth::user()) {
                            $user = Auth::user();
                            $accounttype = $user->accounttype->id;
                            $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room_type)->first();
                        }

                        if ($request->nationality == 1) {
                            $room = $data;

                            if (Auth::user()) {
                                if ($discount_type) {
                                    $price = ResponseHelper::price($room, $request->nationality, $discount_type);
                                } else {
                                    $price = $data->price;
                                }

                            } else {
                                $price = $data->price;
                            }

                            $sign1 = '';
                            $sign2 = 'MMK/Night';

                        } else {

                            $room = $data;

                            if (Auth::user()) {
                                if ($discount_type) {
                                    $price = ResponseHelper::price($room, $request->nationality, $discount_type);

                                } else {
                                    $price = $data->foreign_price;
                                }
                            } else {
                                $price = $data->foreign_price;
                            }

                            $sign1 = '$';
                            $sign2 = '/Night';

                        }

                        $output .= '<div class="col-md-6">
                                    <div class="list-widget shadow2">
                                        <div class="row">
                                        <div class="col-md-12">
                                        <div class="img">
                                        <img src="' . $data->image_path() . '">
                                        </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="detail py-3">
                                                <h4 class="mt-2">' . $roomtype . '</h4>
                                                <p class="price"><i class="fas fa-tag"></i> <span class="price_night">' . $sign1 . '</span> <span  class="room_price">' . $price . '</span> <span class="price_night">' . $sign2 . '</span></p>
                                                <div class="row">
                                                <div class="col-md-6 col-sm-6 mb-2"><span><i class="fas fa-bed blue_fa"></i> ' . $bedtype . '</span></div>
                                                <div class="col-md-6 col-sm-6 mb-2"><span><i class="fas fa-user-friends blue_fa"></i>  ' . $data->adult_qty . ' ' . trans($sleep_msg) . ' </span></div>
                                                </div>
                                                ' . $avaliable_room . '
                                                <div>
                                                ' . $view_detail_btn . '
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>';
                    }
                    return $output . '</div>';

                } else {
                    $room_type = $each->id;
                    $roomtype = $each->roomtype ? $each->roomtype->name : '-';
                    $bedtype = $each->bedtype ? $each->bedtype->name : '-';
                    $view_detail_msg = 'message.button.view_detail';
                    $no_availiable_msg = 'message.no_availiable';
                    $room_qty_notenough_msg = "message.room_qty_notenough_msg";
                    $sleep_msg = "message.sleeps";
                    $we_have_msg = "message.we_have";
                    $left_msg = "message.left";
                    $avaliable_room_qty = ResponseHelper::avaliable_room_qty($each, $request->check_in_date, $request->check_out_date);
                    $avaliable_room = '<p class="text-danger mb-1"> ' . trans($no_availiable_msg) . ' </p>';
                    $view_detail_btn = '<a href="' . url('room/detail') . '/' . $each->id . '?check_in_date=' . $request->check_in_date . '&check_out_date=' . $request->check_out_date . '&room_qty=' . $request->room_qty . '&nationality=' . $request->nationality . '&extra_bed_qty=' . $request->extra_bed_qty . '&guest=1" class="btn btn-view-detail"> ' . trans($view_detail_msg) . ' </a>';

                    if ($avaliable_room_qty > 0) {
                        $avaliable_room = '<p class="text-danger mb-1"><span class="fa fa-warning text-danger"></span> ' . trans($we_have_msg) . ' ' . $avaliable_room_qty . ' ' . trans($left_msg) . ' </p>';
                    }

                    if ($avaliable_room_qty >= $request->room_qty) {
                        $view_detail_btn = '<a href="' . url('room/detail') . '/' . $each->id . '?check_in_date=' . $request->check_in_date . '&check_out_date=' . $request->check_out_date . '&room_qty=' . $request->room_qty . '&nationality=' . $request->nationality . '&extra_bed_qty=' . $request->extra_bed_qty . '&guest=1" class="btn btn-view-detail"> ' . trans($view_detail_msg) . ' </a>';
                    } else {
                        $view_detail_btn = '<p class="text-danger mb-1">' . trans($room_qty_notenough_msg) . '</p>';
                    }

                    if (Auth::user()) {
                        $user = auth()->user();
                        $accounttype = $user->accounttype->id;
                        $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room_type)->first();
                    }

                    if ($request->nationality == 1) {
                        $room = $each;

                        if (Auth::user()) {

                            if ($discount_type) {
                                $price = ResponseHelper::price($room, $request->nationality, $discount_type);
                            } else {
                                $price = $each->price;
                            }

                        } else {
                            $price = $each->price;
                        }

                        $sign1 = '';
                        $sign2 = 'MMK/Night';

                    } else {
                        $room = $each;

                        if (Auth::user()) {
                            if ($discount_type) {
                                $price = ResponseHelper::price($room, $request->nationality, $discount_type);
                            } else {
                                $price = $each->foreign_price;
                            }

                        } else {
                            $price = $each->foreign_price;
                        }

                        $sign1 = '$';
                        $sign2 = '/Night';
                    }

                    return '<div class="list-widget shadow2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="img">
                                        <img src="' . $each->image_path() . '">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="detail">
                                            <h4 class="mt-2">' . $roomtype . '</h4>
                                            <p class="price"><i class="fas fa-tag"></i> <span class="price_night">' . $sign1 . '</span><span class="room_price" > ' . $price . ' </span> <span class="price_night">' . $sign2 . '</span></p>
                                            <div class="row">
                                            <div class="col-md-6 col-sm-6 mb-2"><span><i class="fas fa-bed blue_fa"></i> ' . $bedtype . '</span></div>
                                            <div class="col-md-6 col-sm-6 mb-2"><span><i class="fas fa-user-friends blue_fa"></i>  ' . $each->adult_qty . ' ' . trans($sleep_msg) . '</span></div>
                                            </div>
                                            ' . $avaliable_room . '
                                            <div>
                                            ' . $view_detail_btn . '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>';
                }
            })
            ->rawColumns(['widget'])
            ->make(true);
        // }
    }
}
