<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Credit;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;

class CreditController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        if ($request->ajax()) {

            $credits = Credit::anyTrash($request->trash);

            if ($request->item != '') {
                $credits = $credits->where('id', $request->item);
            }

            return Datatables::of($credits)
                ->addColumn('action', function ($credit) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.credit_reports.edit', ['credit' => $credit->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $credit->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $credit->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $credit->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                // ->addColumn('item_category', function ($credit) {

                //     return $credit->item_category ? $credit->item_category->name : '-';
                // })
                // ->addColumn('item_sub_category', function ($credit) {
                //     return $credit->item_sub_category_id ? $credit->item_sub_category->name : '-';
                // })
                // ->addColumn('image', function ($credit) {
                //     return '<img src="' . $credit->image_path() . '" width="100px;"/>';
                // })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','image'])
                ->make(true);
        }
        return view('backend.admin.credit_reports.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.credit_reports.create', compact('item_category','item', 'item_sub_category'));
    }

    public function store(ItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $credit = new Credit();
        $credit->item_id = $request['item_id'];
        $credit->customer_id = $request['customer_id'];
        $credit->item_name = $request['item_name'];
        $credit->original_amount = $request['original_amount'];
        $credit->paid_amount = $request['paid_amount'];
        $credit->remain_amount = $request['remain_amount'];
        $credit->save();

        activity()
            ->performedOn($credit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Cerdit Report  (Admin Panel'])
            ->log(' Cerdit Report  is created ');

        return redirect()->route('admin.credit_reports.index')->with('success', 'Successfully Created');
    }

    public function show(Credit $credit)
    {
        return view('backend.admin.credit_reports.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $credit = Credit::findOrFail($id);
        $item = Item::where('trash', 0)->get();
        return view('backend.admin.credit_reports.edit', compact('credit', 'item'));
    }

    public function update(ItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $credit = Credit::findOrFail($id);
        $credit->item_id = $request['item_id'];
        $credit->customer_id = $request['customer_id'];
        $credit->item_name = $request['item_name'];
        $credit->original_amount = $request['original_amount'];
        $credit->paid_amount = $request['paid_amount'];
        $credit->remain_amount = $request['remain_amount'];
        $credit->update();

        activity()
            ->performedOn($credit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel'])
            ->log('Credit Report  is updated');

        return redirect()->route('admin.credit_reports.index')->with('success', 'Successfully Updated');
    }

    public function destroy(Credit $credit)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $credit->delete();
        activity()
            ->performedOn($credit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel'])
            ->log(' Credit Report is deleted ');

        return ResponseHelper::success();
    }

    public function trash(Credit $credit)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $credit->trash();
        activity()
            ->performedOn($credit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel)'])
            ->log(' Credit Report is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(Credit $credit)
    {
        $credit->restore();
        activity()
            ->performedOn($credit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel'])
            ->log(' Credit Report is restored from trash ');

        return ResponseHelper::success();
    }
}
