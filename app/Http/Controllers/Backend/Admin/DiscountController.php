<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Rooms;
use App\Models\Discounts;
use App\Models\AccountType;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\DiscountsRequest;

class DiscountController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_discount')) {
            abort(404);
        }

        if ($request->ajax()) {
            $discounts = Discounts::anyTrash($request->trash)->with('accounttype','item')->orderBy('id', 'desc');
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
                ->addColumn('customer', function ($discount) {
                    $customer = $discount->accounttype ? $discount->accounttype->name : '-';
                    return '<ul class="list-group">
                        <li class="list-group-item"> '. $customer. '</li>
                        </ul>';

                })
                ->addColumn('item', function ($discount) {
                    $item = $discount->item ? $discount->item->name : '-';
                    return '<ul class="list-group">
                        <li class="list-group-item"> '. $item. '</li>
                        </ul>';

                })
                ->addColumn('discount_percentage', function ($discount) {
                    $discount_percentage_mm = $discount->discount_percentage_mm ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item"> % - ' . $discount_percentage_mm . '</li>
                        </ul>';

                })
                ->addColumn('discount_amount', function ($discount) {
                    $discount_amount_mm = $discount->discount_amount_mm ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item">MMK - ' . $discount_amount_mm . '</li>
                        </ul>';

                })
                ->addColumn('addon_percentage', function ($discount) {
                    $addon_percentage_mm = $discount->addon_percentage_mm ?? "-";

                    return '<ul class="list-group">
                        <li class="list-group-item"> % - ' . $addon_percentage_mm . '</li>
                        </ul>';
                })
                ->addColumn('addon_amount', function ($discount) {
                    $addon_amount_mm = $discount->addon_amount_mm ?? "-";
                    return '<ul class="list-group">
                        <li class="list-group-item">MMK - ' . $addon_amount_mm . '</li>
                        </ul>';
                })
               
                ->rawColumns(['action', 'customer','item', 'discount_percentage', 'discount_amount', 'addon_percentage', 'addon_amount'])
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
        $items = Item::where('trash', '0')->get();

        return view('backend.admin.discounts.create', compact('user_account_type', 'items'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_discount')) {
            abort(404);
        }

        $discount = new Discounts();
        $discount->user_account_id = $request['user_account_id'];
        $discount->item_id = $request['item_id'];
        $discount->discount_percentage_mm = $request['discount_percentage_mm'];
        $discount->discount_amount_mm = $request['discount_amount_mm'];
        $discount->addon_percentage_mm = $request['addon_percentage_mm'];
        $discount->addon_amount_mm = $request['addon_amount_mm'];
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
        $items = Item::where('trash', '0')->get();
        return view('backend.admin.discounts.edit', compact('discount', 'user_account_type', 'items'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_discount')) {
            abort(404);
        }

        $discount = Discounts::findOrFail($id);
        $discount->user_account_id = $request['user_account_id'];
        $discount->item_id = $request['item_id'];
        $discount->discount_percentage_mm = $request['discount_percentage_mm'];
        $discount->discount_amount_mm = $request['discount_amount_mm'];
        $discount->addon_percentage_mm = $request['addon_percentage_mm'];
        $discount->addon_amount_mm = $request['addon_amount_mm'];

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
