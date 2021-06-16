<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\UserCreditCard;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserCreditCardController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('view_user_credit')) {
            abort(404);
        }

        if ($request->ajax()) {
            $usercreditcards = UserCreditCard::anyTrash($request->trash)->with('user');
            return Datatables::of($usercreditcards)
                ->addColumn('action', function ($usercreditcards) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('delete_user_credit')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $usercreditcards->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $usercreditcards->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $usercreditcards->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('expire_date', function ($usercreditcards) {
                    $months = config('app.month');
                    $years = config('app.year');
                    $month = $months[$usercreditcards->expire_month];
                    $year = $years[$usercreditcards->expire_year];

                    return "${month} / ${year}";
                })
                ->addColumn('credit_type', function ($usercreditcards) {
                    $credit_type = $usercreditcards->cardtype->name;
                    return "${credit_type}";
                })
                ->filterColumn('credit_type', function ($query, $keyword) {
                    $query->whereHas('cardtype', function ($q1) use ($keyword) {
                        $q1->where('name', 'LIKE', "%{$keyword}%");

                    });
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.usercreditcard.index');
    }

    public function destroy(UserCreditCard $usercreditcard)
    {

        if (!$this->getCurrentAuthUser('admin')->can('delete_user_credit')) {
            abort(404);
        }
        $usercreditcard->delete();
        activity()
            ->performedOn($tax)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Credit Card (Admin Panel)'])
            ->log(' User CreditCard is deleted');

        return ResponseHelper::success();
    }

    public function trash(UserCreditCard $usercreditcard)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user_credit')) {
            abort(404);
        }
        $usercreditcard->trash();
        activity()
            ->performedOn($usercreditcard)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Credit Card (Admin Panel)'])
            ->log(' User CreditCard is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(UserCreditCard $usercreditcard)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user_credit')) {
            abort(404);
        }
        $usercreditcard->restore();
        activity()
            ->performedOn($usercreditcard)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Credit Card (Admin Panel)'])
            ->log(' User CreditCard is restored from trash');

        return ResponseHelper::success();
    }
}
