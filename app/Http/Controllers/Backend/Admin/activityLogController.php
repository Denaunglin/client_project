<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class activityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $activity_logs = Activity::orderBy('id', 'desc');
            if ($daterange) {
                $activity_logs = $activity_logs->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            return Datatables::of($activity_logs)
                ->addColumn('action', function ($activity_log) use ($request) {
                    $restore_btn = '';
                    $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $activity_log->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                    return " ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('source', function ($activity_log) {
                    return $activity_log->getExtraProperty('source') ?? '-';
                })
                ->addColumn('causer', function ($activity_log) {
                    return $activity_log->causer ? $activity_log->causer->name : '-';
                })
                ->addColumn('subject', function ($activity_log) {
                    return $activity_log->subject ? ' <ul class="list-group text-left"><li class="list-group-item"> ID :  ' . $activity_log->id . ' </li>  <li class="list-group-item"> Model :  ' . $activity_log->subject_type . ' </li></ul>' : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action', 'subject'])
                ->make(true);
        }

        return view('backend.admin.activity_log.index');

    }

    public function destroy(Activity $activity_log)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('delete_account_type')) {
        //     abort(404);
        // }

        $activity_log->delete();
        return ResponseHelper::success();
    }
}
