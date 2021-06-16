<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\FontConvert;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiProfileUpdateRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingRoomResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProfileEditResource;
use App\Http\Resources\ProfileResource;
use App\Models\Booking;
use App\Models\RoomSchedule;
use App\Models\User;
use App\Models\UserCreditCard;
use App\Models\UserNrcPicture;
use App\Models\UserProfile;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ClientController extends Controller
{
    //profile

    public function profile()
    {
        try {
            $user = Auth::user();
            $data = new ProfileResource($user);
            return ResponseHelper::success($data);
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');

        }
    }

    public function profileEdit()
    {
        try {

            $user = Auth::user();
            $data = new ProfileEditResource($user);

            return ResponseHelper::success($data);

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }

    }

    public function profileUpdate(ApiProfileUpdateRequest $request)
    {

        try {

            $user = auth()->user();

            if ($user) {
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

                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->phone = $request['phone'];
                $user->nrc_passport = $request['nrc_passport'];
                $user->date_of_birth = $request['date_of_birth'];
                $user->gender = $request['gender'];
                $user->address = $request['address'];
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

                return ResponseHelper::successMessage();

            } else {
                return ResponseHelper::failedMessage('User not found');
            }

        } catch (\Exception $e) {

            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    public function AddUserCard(Request $request)
    {
        try {
            $user = Auth::user();
            $usercard = new UserCreditCard();
            $usercard->user_id = $user->id;
            $usercard->credit_type = $request->credit_type;
            $usercard->account_name = $request->account_name;
            $usercard->credit_no = $request->credit_no;
            $usercard->expire_month = $request->expire_month;
            $usercard->expire_year = $request->expire_year;
            $usercard->save();

            return ResponseHelper::successMessage('Successfully added new credit card');

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    public function UserCardUpdate(Request $request)
    {
        try {

            $user = Auth::user();
            $usercreditcard = UserCreditCard::find($request->id);
            $usercreditcard->user_id = $user->id;
            $usercreditcard->credit_type = $request->credit_type;
            $usercreditcard->credit_no = $request->credit_no;
            $usercreditcard->expire_month = $request->expire_month;
            $usercreditcard->expire_year = $request->expire_year;
            $usercreditcard->account_name = $request->account_name;
            $usercreditcard->update();

            return ResponseHelper::successMessage('Successfully Update Credit Card !');
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    public function UserCardDelete(Request $request)
    {
        try {
            $usercreditcard = UserCreditCard::where('id', $request->id)->first();
            if ($usercreditcard) {
                $usercreditcard->delete();
                return ResponseHelper::successMessage('Credit card have been deleted !');
            }
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    //booking

    public function booking(Request $request)
    {
        try {

            $today = date('Y-m-d');
            $user = Auth::user();
            $bookings = Booking::where('client_user', $user->id);

            if ($request->option == 1) {
                $bookings = $bookings->where('check_in', '<', $today);
            } else if ($request->option == 2) {
                $bookings = $bookings->where('check_in', '>=', $today);
            }

            $bookings = $bookings->paginate(10);

            return BookingResource::collection($bookings)->additional(['result' => 1, 'message' => 'success']);

        } catch (\Exception $e) {

            return ResponseHelper::failedMessage('Something Wrong. ' . $e->getMessage());
        }
    }

    public function bookingDetail(Request $request)
    {
        try {

            $booking = Booking::where('booking_number', $request->booking_number)->First();
            if ($booking) {
                $data = new BookingRoomResource($booking);
                return ResponseHelper::success($data);

            } else {
                return ResponseHelper::fail('Booking not found');
            }

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }

    }

    public function Cancellation(Request $request)
    {
        try {

            $booking = Booking::where('booking_number', $request->booking_number)->first();
            if ($booking) {
                $booking->cancellation = '1';
                $booking->status = '2';
                $booking->cancellation_remark = FontConvert::zg2uni($request->cancellation_remark);
                $booking->update();

                $roomschedule = RoomSchedule::where('booking_id', $request->id)->get();

                if ($roomschedule) {
                    foreach ($roomschedule as $data) {
                        $data->delete();
                    }
                }

            } else {
                return ResponseHelper::fail('Booking not found');
            }

            return ResponseHelper::successMessage();

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    //notification

    public function Notification()
    {
        try {

            $noti = Auth::user()->notifications();
            $notifications = $noti->paginate(10);

            $notis = $noti->where('read_at', null)->get();
            $unread_noti_count = $notis ? count($notis) : 0;

            return NotificationResource::collection($notifications)->additional(['result' => 1, 'message' => 'success', 'unread_noti_count' => $unread_noti_count]);

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    public function markAsRead($notification_id)
    {
        try {
            $notifications = Auth::user()->notifications;
            $notification = $notifications->where('id', $notification_id)->first();

            if ($notification) {
                $notification->markAsRead();
                return ResponseHelper::successMessage();
            }

            return ResponseHelper::fail('Notification not found');
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    public function notificationDelete($notification_id)
    {
        try {
            $notifications = Auth::user()->notifications;
            $notification = $notifications->where('id', $notification_id)->first();
            if ($notification) {
                $notification->delete();
                return ResponseHelper::successMessage();
            }
            return ResponseHelper::failedMessage('Notification not found');
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    public function show($notification_id)
    {
        try {
            $notifications = Auth::user()->notifications;
            $notification = $notifications->where('id', $notification_id)->first();

            if ($notification) {
                $notification->markAsRead();
                return ResponseHelper::success(new NotificationResource($notification));
            }
            return ResponseHelper::failedMessage('Notification Not Found');

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

}
