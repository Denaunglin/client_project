<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\ItemLedger;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;

class ItemLedgerController extends Controller
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
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            
            $item_ledgers = ItemLedger::anyTrash($request->trash)->orderBy('id','desc');
            if ($daterange) {
                $item_ledgers = ItemLedger::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            if ($request->item != '') {
                $item_ledgers = $item_ledgers->where('item_id', $request->item);
            }

            // if ($request->item_category != '') {
            //     $items = $items->where('item_category_id', $request->item_category);
            // }

            // if ($request->item_sub_category != '') {
            //     $items = $items->where('item_sub_category_id', $request->item_sub_category);

            // }

            return Datatables::of($item_ledgers)
                ->addColumn('action', function ($item_ledger) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $item_ledger->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $item_ledger->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $item_ledger->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('barcode', function ($item_ledger) {

                    return $item_ledger->item ? $item_ledger->item->barcode : '-';
                })
                ->addColumn('item_id', function ($item_ledger) {

                    return $item_ledger->item ? $item_ledger->item->name : '-';
                })
                ->addColumn('unit', function ($item_ledger) {

                    return $item_ledger->item ? $item_ledger->item->unit : '-';
                })
                ->addColumn('item_sub_category', function ($item_ledger) {
                    return $item_ledger->item->item_sub_category_id ? $item_ledger->item->item_sub_category->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','barcode','item_id','unit','item_sub_category'])
                ->make(true);
        }
        return view('backend.admin.item_ledgers.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.item_ledgers.create', compact('item_category','item', 'item_sub_category'));
    }

    public function store(ItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $item_ledgers = new ItemLedger();
        $item_ledgers->item_id = $request['item_id'];
        $item_ledgers->opening_qty = $request['opening_qty'];
        $item_ledgers->buying_buy = $request['buying_buy'];
        $item_ledgers->buying_back = $request['buying_back'];
        $item_ledgers->selling_sell = $request['selling_sell'];
        $item_ledgers->selling_back = $request['selling_back'];
        $item_ledgers->adjust_in = $request['adjust_in'];
        $item_ledgers->adjust_out = $request['adjust_out'];
        $item_ledgers->closing_qty = $request['closing_qty'];
        $item_ledgers->save();

        activity()
            ->performedOn($item_ledgers)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' New Item Ledger is created ');

        return redirect()->route('admin.item_ledgers.index')->with('success', 'Successfully Created');
    }

    public function show(ItemLedger $item_ledger)
    {
        return view('backend.admin.item_ledgers.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $item_ledger = ItemLedger::findOrFail($id);
        $item= Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();

        return view('backend.admin.item_ledgers.edit', compact('item_ledger','item', 'item_category', 'item_sub_category'));
    }

    public function update(ItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $item_ledgers = ItemLedger::findOrFail($id);
        $item_ledgers->item_id = $request['item_id'];
        $item_ledgers->opening_qty = $request['opening_qty'];
        $item_ledgers->buying_buy = $request['buying_buy'];
        $item_ledgers->buying_back = $request['buying_back'];
        $item_ledgers->selling_sell = $request['selling_sell'];
        $item_ledgers->selling_back = $request['selling_back'];
        $item_ledgers->adjust_in = $request['adjust_in'];
        $item_ledgers->adjust_out = $request['adjust_out'];
        $item_ledgers->closing_qty = $request['closing_qty'];
        $item_ledgers->update();

        activity()
            ->performedOn($item_ledgers)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log('Item Ledger is updated');

        return redirect()->route('admin.item_ledgers.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ItemLedger $item_ledger)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $item_ledger->delete();
        activity()
            ->performedOn($item_ledger)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  Ledger  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ItemLedger $item_ledger)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $item_ledger->trash();
        activity()
            ->performedOn($item_ledger)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel)'])
            ->log(' Item Ledger  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ItemLedger $item_ledger)
    {
        $item_ledger->restore();
        activity()
            ->performedOn($item_ledger)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item Ledger is restored from trash ');

        return ResponseHelper::success();
    }
}
