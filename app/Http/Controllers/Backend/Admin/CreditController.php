<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\User;
use App\Models\Credit;
use App\Models\Cashbook;
use App\Models\SellItems;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
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

            $credits = Credit::anyTrash($request->trash)->orderBy('id','desc');
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

                    $detail_btn = '<a class="detail text text-primary" href="' . route('admin.credit_reports.detail', ['credit_report' => $credit->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';


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
                    $id = unserialize($credit->item_id);
                    $item = Item::findMany($id);
                    $count =count($item) ;
                    // $item_name = $credit->item ? $credit->item->name : '-';
                    // $item_code = $credit->item ? $credit->item->barcode : '-';
                    return '<ul class="list-group">
                    <li class="list-group-item"> Total Item : '.$count.'</li>
                </ul>';
                })
                ->addColumn('paid_status', function ($credit) {
                 if($credit->paid_status == 0){
                    return '<span class="badge badge-success">Paided</span>';
                 }else{
                    return '<span class="badge badge-warning">UnPaid</span>';
                 }
                   
                })
                ->addColumn('customer', function ($credit) {
                    if($credit->customer){
                        $customer_name = $credit->customer ? $credit->customer->name : '-';
                        $customer_phone = $credit->customer ? $credit->customer->phone : '-';
                        $customer_address = $credit->customer ? $credit->customer->address : '-';
    
                        return '<ul class="list-group">
                            <li class="list-group-item">'.$customer_name.'</li>
                            <li class="list-group-item">('.$customer_phone.')</li>
                            <li class="list-group-item">('.$customer_address.')</li>
                        </ul>';
                    }else{
                        return 'Default Customer';
                    }
                 
                })
               
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','item','customer','paid_status'])
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
        $credit->item_id = serialize($request['item_id']);
        $credit->qty = $request['qty'];
        $credit->customer_id = $request['customer_id'];
        $credit->origin_amount = $request['origin_amount'];
        $credit->paid_amount = $request['paid_amount'];
        $credit->credit_amount = $request['credit_amount'];
        $credit->paid_date = $request['paid_date'];
        $credit->paid_times = $request['paid_times'];
        $credit->late_id= null;
        $credit->paid_status= $request['paid_status'];
        $credit->save();

        if($request->paid_status == 0){
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
        }

        $item = Item::findOrFail($request->item_id);

        $sell_item = new SellItems();
        $sell_item->item_id = $item->id;
        $sell_item->customer_id = $request['customer_id'];
        $sell_item->item_category_id = $item->item_category_id;
        $sell_item->item_sub_category_id = $item->item_sub_category_id;
        $sell_item->qty = $request['qty'];
        $sell_item->price = $request['origin_amount'];
        $sell_item->discount = 0;
        $sell_item->net_price = $request['origin_amount'];
        $sell_item->save();

        $shop_storage = ShopStorage::where('item_id',$item->id)->first();
        $open_qty = $shop_storage->qty ? $shop_storage->qty : 0 ;

        if($shop_storage){
            $qty = ($shop_storage->qty) - ($sell_item->qty);
            $shop_storage->qty = $qty;
            $shop_storage->update();
        }else{
            $shop_storage = new ShopStorage();
            $shop_storage->item_id = $item->id;
            $shop_storage->qty = $sell_item->qty;
            $shop_storage->save();
        }

           $item_ledger= new ItemLedger();
            $item_ledger->item_id = $item->id;
            $item_ledger->opening_qty = $open_qty;
            $item_ledger->buying_buy = '0';
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = $request->qty;
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->closing_qty = $shop_storage->qty;
            $item_ledger->save();

        
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
        $credit->item_id = serialize($request['item_id']);
        $credit->qty = $request['qty'];
        $credit->customer_id = $request['customer_id'];
        $credit->origin_amount = $request['origin_amount'];
        $credit->paid_amount = $request['paid_amount'];
        $credit->credit_amount = $request['credit_amount'];
        $credit->paid_date = $request['paid_date'];
        $credit->paid_times = $request['paid_times'];
        $credit->late_id= 0;
        $credit->paid_status= $request['paid_status'];

        $credit->update();

        if($request->paid_status == 0){
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
        }

        activity()
            ->performedOn($credit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel'])
            ->log('Credit Report  is updated');

        return redirect()->route('admin.credit_reports.index')->with('success', 'Successfully Updated');
    }

    public function destroy(Credit $credit_report)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $credit_report->delete();
        activity()
            ->performedOn($credit_report)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel'])
            ->log(' Credit Report is deleted ');

        return ResponseHelper::success();
    }

    public function trash(Credit $credit_report)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $credit_report->trash= 1;
        $credit_report->update();
        activity()
            ->performedOn($credit_report)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel)'])
            ->log(' Credit Report is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(Credit $credit_report)
    {
        $credit_report->trash= 0;
        $credit_report->update();   
             activity()
            ->performedOn($credit_report)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Credit Report  (Admin Panel'])
            ->log(' Credit Report is restored from trash ');

        return ResponseHelper::success();
    }

    public function detail($id){
        $credit = Credit::findOrFail($id);
        $find = unserialize($credit->item_id);
        $item_data = Item::findMany($find);
        return view('backend.admin.credit_reports.detail',compact('credit','item_data'));
    }
}
