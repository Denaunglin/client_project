<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\HelperFunction;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\sendNotifications;
use App\Http\Traits\AuthorizePerson;
use App\Models\OneSignalSubscriber;
use App\Models\sendNotification;
use App\Models\User;
use App\Notifications\sendCustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notification;
use Yajra\DataTables\DataTables;

class sendNotificationController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_sendNotification')) {
            abort(404);
        }

        if ($request->ajax()) {
            $sendNoti = sendNotification::anyTrash($request->trash)->orderBy('id', 'desc')->with('user');
            return Datatables::of($sendNoti)
                ->addColumn('action', function ($sendNoti) use ($request) {
                    $restore_btn = '';
                    $trash_or_delete_btn = '';
                    $detail_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('delete_sendNotification')) {

                        if ($this->getCurrentAuthUser('admin')->can('edit_sendNotification')) {
                            $detail_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.sendnotifications.detail', ['sendnotification' => $sendNoti->id]) . '"><i class="far fa-file fa-lg"></i></a>';
                        }

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $sendNoti->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $sendNoti->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $sendNoti->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return " ${restore_btn} ${detail_btn} ${trash_or_delete_btn}";

                })
                ->addColumn('description', function ($sendNoti) {
                    return Str::limit($sendNoti->description, 90);
                })
                ->addColumn('user_id', function ($sendNoti) {
                    $name = $sendNoti->user ? $sendNoti->user->name : '-';
                    $email = $sendNoti->user ? $sendNoti->user->email : '-';
                    $phone = $sendNoti->user ? $sendNoti->user->phone : '-';
                    return '<ul class="list-group">
                            <li class="list-group-item">Name - ' . $name . '</li>
                            <li class="list-group-item">Email - ' . $email . '</li>
                            <li class="list-group-item">Phone - ' . $phone . '</li>
                        </ul>';

                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->filterColumn('user_id', function ($query, $keyword) {
                    $query->whereHas('user', function ($q1) use ($keyword) {
                        $q1->where('name', 'LIKE', "%{$keyword}%");
                        $q1->orWhere('email', 'LIKE', "%{$keyword}%");
                        $q1->orWhere('phone', 'LIKE', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action', 'description', 'user_id'])
                ->make(true);
        }
        return view('backend.admin.sendNotification.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_sendNotification')) {
            abort(404);
        }
        $user = User::where('trash', 0)->get();
        return view(('backend.admin.sendNotification.create'), compact('user'));
    }

    public function store(sendNotifications $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_sendNotification')) {
            abort(404);
        }

        $user_count = count($request->user_id);
        $user_id = $request->user_id;

        foreach ($user_id as $data) {
            $user = User::where('id', $data)->first();
            $subscribers = OneSignalSubscriber::where('user_id', $user->id)->get();
            $subscriber = $subscribers;
            $subscriber_count = $subscribers->count();

            $sendnotification = new sendNotification();
            $sendnotification->title = $request['title'];
            $sendnotification->description = $request['description'];
            $sendnotification->link = $request['link'];
            $sendnotification->user_id = $data;
            $sendnotification->save();

            $details = [
                'title' => $request->title,
                'detail' => $request->description,
                'link' => url(''),
                'web_link' => $request->link,
                'deep_link' => config('deep_link.host'),
                'order_id' => $sendnotification->id,
            ];

            Notification::send($user, new sendCustomNotification($details));

            if ($subscriber) {
                $app_id = config('app.signal_app_id');
                $latested_notification_id = $user->notifications->last()->id;

                $details = [
                    'title' => $request->title,
                    'detail' => $request->description,
                    'link' => url(''),
                    'web_link' => $request->link,
                    'deep_link' => config('deep_link.host') . '/booking_id=&noti_id=' . $latested_notification_id,
                    'order_id' => $sendnotification->id,
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
                activity()
                    ->performedOn($sendnotification)
                    ->causedBy(auth()->guard('admin')->user())
                    ->withProperties(['source' => 'Send Notification (Admin Panel)'])
                    ->log('Notification is sended');

            } else {
                return ResponseHelper::failedMessage('User not subscribe notificaiton !');
            }
        }

        return redirect()->route('admin.sendnotifications.index')->with('success', 'Successfully Created');
    }

    public function show(sendNotification $sendnotification)
    {
        return view('backend.admin.sendNotification.show', compact('sendnotification'));
    }

    public function detail(sendNotification $sendnotification)
    {
        return view('backend.admin.sendNotification.detail', compact('sendnotification'));
    }

    public function destroy(sendNotification $sendnotification)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_sendNotification')) {
            abort(404);
        }

        $sendnotification->delete();
        activity()
            ->performedOn($sendnotification)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Send Notification (Admin Panel)'])
            ->log('Notification is deleted');

        return ResponseHelper::success();
    }

    public function trash(sendNotification $sendnotification)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_sendNotification')) {
            abort(404);
        }

        $sendnotification->trash();
        activity()
            ->performedOn($sendnotification)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Send Notification (Admin Panel)'])
            ->log('Notification is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(sendNotification $sendnotification)
    {
        $sendnotification->restore();
        activity()
            ->performedOn($sendnotification)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Send Notification (Admin Panel)'])
            ->log('Notification is restored from trash');

        return ResponseHelper::success();
    }

}
