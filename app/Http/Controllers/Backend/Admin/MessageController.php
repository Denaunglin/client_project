<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Helper\SpamFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\Messages;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MessageController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_message')) {
            abort(404);
        }

        if ($request->ajax()) {
            $messages = Messages::anyTrash($request->trash)->orderBy('id', 'desc')->get();

            foreach ($messages as $check) {
                if (SpamFilter::result($check->name) || SpamFilter::result($check->email) || SpamFilter::result($check->message)) {
                    $check->delete();
                }
            }

            return Datatables::of($messages)
                ->addColumn('action', function ($messages) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';

                    if ($request->trash == 1) {
                        $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $messages->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                        $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $messages->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';

                    } else {
                        $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $messages->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                    }

                    return "${detail_btn} ${restore_btn} ${trash_or_delete_btn}";
                })

                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action', 'message'])
                ->make(true);
        }
        return view('backend.admin.message.index');
    }

    public function destroy(Messages $message)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_message')) {
            abort(404);
        }

        $message->delete();

        activity()
            ->performedOn($message)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Message (Admin Panel)'])
            ->log('Message is deleted');

        return ResponseHelper::success();
    }

    public function trash(Messages $message)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_message')) {
            abort(404);
        }

        $message->trash();
        activity()
            ->performedOn($message)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Message (Admin Panel)'])
            ->log('Message is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(Messages $message)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_message')) {
            abort(404);
        }

        $message->restore();

        activity()
            ->performedOn($message)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Message (Admin Panel)'])
            ->log('Message is restored from trash');

        return ResponseHelper::success();
    }
}
