<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountsRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\Discounts;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DiscountController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_discount')) {
            abort(404);
        }

        if ($request->ajax()) {
            $discounts = Discounts::anyTrash($request->trash)->with('accounttype', 'roomtype', 'room')->orderBy('id', 'desc');
            return Datatables::of($discounts)
                ->addColumn('action', function ($discount) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_discount')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.discounts.edit', ['discount' => $discount->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_discount')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $discount->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $discount->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $discount->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->addColumn('roomtype', function ($discount) {
                    $roomtype = $discount->room->roomtype->name;
                    return $roomtype;
                })
                ->addColumn('discount_percentage', function ($discount) {
                    $discount_percentage_mm = $discount->discount_percentage_mm ?? "-";
                    $discount_percentage_foreign = $discount->discount_percentage_foreign ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item">MM - ' . $discount_percentage_mm . '</li>
                        <li class="list-group-item">Foreign - ' . $discount_percentage_foreign . '</li>
                        </ul>';

                })
                ->addColumn('discount_amount', function ($discount) {
                    $discount_amount_mm = $discount->discount_amount_mm ?? "-";
                    $discount_amount_foreign = $discount->discount_amount_foreign ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item">MM - ' . $discount_amount_mm . '</li>
                        <li class="list-group-item">Foreign - ' . $discount_amount_foreign . '</li>
                        </ul>';

                })
                ->addColumn('addon_percentage', function ($discount) {
                    $addon_percentage_mm = $discount->addon_percentage_mm ?? "-";
                    $addon_percentage_foreign = $discount->addon_percentage_foreign ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item">MM - ' . $addon_percentage_mm . '</li>
                        <li class="list-group-item">Foreign - ' . $addon_percentage_foreign . '</li>
                        </ul>';

                })
                ->addColumn('addon_amount', function ($discount) {
                    $addon_amount_mm = $discount->addon_amount_mm ?? "-";
                    $addon_amount_foreign = $discount->addon_amount_foreign ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item">MM - ' . $addon_amount_mm . '</li>
                        <li class="list-group-item">Foreign - ' . $addon_amount_foreign . '</li>
                        </ul>';

                })
                ->filterColumn('roomtype', function ($query, $keyword) {
                    $query->whereHas('room', function ($q1) use ($keyword) {
                        $q1->whereHas('roomtype', function ($q2) use ($keyword) {
                            $q2->where('name', 'LIKE', "%{$keyword}%");
                        });
                    });
                })
                ->rawColumns(['action', 'roomtype', 'discount_percentage', 'discount_amount', 'addon_percentage', 'addon_amount'])
                ->make(true);
        }
        return view('backend.admin.discounts.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_discount')) {
            abort(404);
        }
        $user_account_type = AccountType::where('trash', '0')->get();
        $room_type = Rooms::where('trash', '0')->get();

        return view('backend.admin.discounts.create', compact('user_account_type', 'room_type'));
    }

    public function store(DiscountsRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_discount')) {
            abort(404);
        }

        $discount = new Discounts();
        $discount->user_account_id = $request['user_account_id'];
        $discount->room_type_id = $request['room_type_id'];
        $discount->discount_percentage_mm = $request['discount_percentage_mm'];
        $discount->discount_percentage_foreign = $request['discount_percentage_foreign'];
        $discount->discount_amount_mm = $request['discount_amount_mm'];
        $discount->discount_amount_foreign = $request['discount_amount_foreign'];
        $discount->addon_percentage_mm = $request['addon_percentage_mm'];
        $discount->addon_percentage_foreign = $request['addon_percentage_foreign'];
        $discount->addon_amount_mm = $request['addon_amount_mm'];
        $discount->addon_amount_foreign = $request['addon_amount_foreign'];

        $discount->save();

        activity()
            ->performedOn($discount)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Discount (Admin Panel)'])
            ->log('New Discount is added ');

        return redirect()->route('admin.discounts.index')->with('success', 'Successfully Created');
    }

    public function show(Discounts $discount)
    {
        return view('backend.admin.discounts.show', compact('discounts'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_discount')) {
            abort(404);
        }

        $discount = Discounts::findOrFail($id);
        $user_account_type = AccountType::where('trash', '0')->get();
        $room_type = Rooms::where('trash', '0')->get();
        return view('backend.admin.discounts.edit', compact('discount', 'user_account_type', 'room_type'));
    }

    public function update(DiscountsRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_discount')) {
            abort(404);
        }

        $discount = Discounts::findOrFail($id);
        $discount->user_account_id = $request['user_account_id'];
        $discount->room_type_id = $request['room_type_id'];
        $discount->discount_percentage_mm = $request['discount_percentage_mm'];
        $discount->discount_percentage_foreign = $request['discount_percentage_foreign'];
        $discount->discount_amount_mm = $request['discount_amount_mm'];
        $discount->discount_amount_foreign = $request['discount_amount_foreign'];
        $discount->addon_percentage_mm = $request['addon_percentage_mm'];
        $discount->addon_percentage_foreign = $request['addon_percentage_foreign'];
        $discount->addon_amount_mm = $request['addon_amount_mm'];
        $discount->addon_amount_foreign = $request['addon_amount_foreign'];

        $discount->update();
        activity()
            ->performedOn($discount)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Discount (Admin Panel)'])
            ->log('Discount is updated');

        return redirect()->route('admin.discounts.index')->with('success', 'Successfully Updated');
    }

    public function destroy(Discounts $discount)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_discount')) {
            abort(404);
        }

        $discount->delete();
        activity()
            ->performedOn($discount)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Discount (Admin Panel)'])
            ->log('Discount is deleted');

        return ResponseHelper::success();
    }

    public function trash(Discounts $discount)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_discount')) {
            abort(404);
        }
        $discount->trash();
        activity()
            ->performedOn($discount)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Discount (Admin Panel)'])
            ->log('Discount is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(Discounts $discount)
    {
        $discount->restore();
        activity()
            ->performedOn($discount)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Discount (Admin Panel)'])
            ->log('Discount is restored from trash');

        return ResponseHelper::success();
    }
}
