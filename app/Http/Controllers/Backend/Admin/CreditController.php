<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\User;
use App\Models\Credit;
use App\Models\Cashbook;
use App\Models\SellItems;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreditRequest;
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
        $customer = User::where('trash',0)->get();
        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;

            $credits = Credit::anyTrash($request->trash);
            if ($daterange) {
                $credits = Credit::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            if ($request->item != '') {
                $credits = $credits->where('item_id', $request->item);
            }
            if ($request->customer != '') {
                $credits = $credits->where('customer_id', $request->customer);
            }

            return Datatables::of($credits)
                ->addColumn('action', function ($credit) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.credit_reports.edit', ['credit_report' => $credit->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
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
                ->addColumn('item', function ($credit) {
                    $item_name = $credit->item ? $credit->item->name : '-';
                    $item_code = $credit->item ? $credit->item->barcode : '-';
                    return '<ul class="list-group">
                        <li class="list-group-item">'.$item_name.'</li>
                        <li class="list-group-item">('.$item_code.')</li>
                    </ul>';
                })
                ->addColumn('customer', function ($credit) {
                    $customer_name = $credit->customer ? $credit->customer->name : '-';
                    $customer_phone = $credit->customer ? $credit->customer->phone : '-';
                    $customer_address = $credit->customer ? $credit->customer->address : '-';

                    return '<ul class="list-group">
                        <li class="list-group-item">'.$customer_name.'</li>
                        <li class="list-group-item">('.$customer_phone.')</li>
                        <li class="list-group-item">('.$customer_address.')</li>
                    </ul>';
                })
               
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','item','customer'])
                ->make(true);
        }
        return view('backend.admin.credit_reports.index',compact('item','customer'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $customer = User::where('trash',0)->get();
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.credit_reports.create', compact('item_category','customer','item', 'item_sub_category'));
    }

    public function store(CreditRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $credit = new Credit();
        $credit->item_id = $request['item_id'];
        $credit->qty = $request['qty'];
        $credit->customer_id = $request['customer_id'];
        $credit->origin_amount = $request['origin_amount'];
        $credit->paid_amount = $request['paid_amount'];
        $credit->credit_amount = $request['credit_amount'];
        $credit->paid_date = $request['paid_date'];
        $credit->paid_times = $request['paid_times'];
        $credit->late_id= null;
        $credit->save();

        $cash_book = new Cashbook();
        $cash_book->cashbook_income = $credit->paid_amount ;
        $cash_book->cashbook_outgoing =  0 ;
        $cash_book->buying_id = null;
        $cash_book->selling_id = null;
        $cash_book->service_id = null;
        $cash_book->expense_id = null;
        $cash_book->credit_id = $credit->id;
        $cash_book->return_id = null;
        $cash_book->save();

        $item = Item::findOrFail($request->item_id);

        $sell_item = new SellItems();
        $sell_item->barcode = $item->barcode;
        $sell_item->item_id = $item->id;
        $sell_item->customer_id = $request['customer_id'];
        $sell_item->unit = $item->unit;
        $sell_item->item_category_id = $item->item_category_id;
        $sell_item->item_sub_category_id = $item->item_sub_category_id;
        $sell_item->qty = $request['qty'];
        $sell_item->price = $request['origin_amount'];
        $sell_item->discount = 0;
        $sell_item->net_price = $request['origin_amount'];
        $sell_item->save();
        
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
        $customer = User::where('trash',0)->get();
        $credit = Credit::findOrFail($id);
        $item = Item::where('trash', 0)->get();
        return view('backend.admin.credit_reports.edit', compact('credit','customer', 'item'));
    }

    public function update(CreditRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $credit = Credit::findOrFail($id);
        $credit->item_id = $request['item_id'];
        $credit->qty = $request['qty'];
        $credit->customer_id = $request['customer_id'];
        $credit->origin_amount = $request['origin_amount'];
        $credit->paid_amount = $request['paid_amount'];
        $credit->credit_amount = $request['credit_amount'];
        $credit->paid_date = $request['paid_date'];
        $credit->paid_times = $request['paid_times'];
        $credit->late_id= null;
        $credit->update();

        $cash_book =  new Cashbook();
        $cash_book->cashbook_income = $credit->paid_amount ;
        $cash_book->cashbook_outgoing =  0 ;
        $cash_book->buying_id = null;
        $cash_book->selling_id = null;
        $cash_book->service_id = null;
        $cash_book->expense_id = null;
        $cash_book->credit_id = $credit->id;
        $cash_book->return_id = null;
        $cash_book->save();

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
