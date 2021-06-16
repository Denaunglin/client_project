<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\ExtraBedPrice;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExtraBedPriceController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_extra_bed_price')) {
            abort(404);
        }

        if ($request->ajax()) {
            $extrabedprices = ExtraBedPrice::anyTrash($request->trash)->with('accounttype')->orderBy('id', 'desc')->get();
            return Datatables::of($extrabedprices)
                ->addColumn('action', function ($extrabedprice) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_extra_bed_price')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.extrabedprices.edit', ['extrabedprice' => $extrabedprice->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_extra_bed_price')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $extrabedprice->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $extrabedprice->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $extrabedprice->id . '"><i class="fas fa-trash fa-lg"></i></a>';

                        }
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('add_extrabed_price', function ($extrabedprice) {
                    $extrabedprice_mm = $extrabedprice->add_extrabed_price_mm ? $extrabedprice->add_extrabed_price_mm : '0';
                    $extrabedprice_foreign = $extrabedprice->add_extrabed_price_foreign ? $extrabedprice->add_extrabed_price_foreign : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">MM - ' . $extrabedprice_mm . '</li>
                            <li class="list-group-item">Foreign - ' . $extrabedprice_foreign . '</li>
                            </ul>';

                })
                ->addColumn('subtract_extrabed_price', function ($extrabedprice) {
                    $extrabedprice_mm = $extrabedprice->subtract_extrabed_price_mm ? $extrabedprice->subtract_extrabed_price_mm : '0';
                    $extrabedprice_foreign = $extrabedprice->subtract_extrabed_price_mm ? $extrabedprice->subtract_extrabed_price_mm : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">MM - ' . $extrabedprice_mm . '</li>
                            <li class="list-group-item">Foreign - ' . $extrabedprice_foreign . '</li>
                            </ul>';

                })

                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action', 'add_extrabed_price', 'subtract_extrabed_price'])
                ->make(true);
        }
        return view('backend.admin.extrabedprices.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_extra_bed_price')) {
            abort(404);
        }
        $accounttype = AccountType::where('trash', '0')->get();

        return view('backend.admin.extrabedprices.create', compact('accounttype'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_extra_bed_price')) {
            abort(404);
        }
        $extrabedprice = new ExtraBedPrice();
        $extrabedprice->user_account_id = $request['user_id'];
        $extrabedprice->add_extrabed_price_mm = $request->add_extrabed_price_mm;
        $extrabedprice->add_extrabed_price_foreign = $request->add_extrabed_price_foreign;
        $extrabedprice->subtract_extrabed_price_mm = $request->subtract_extrabed_price_mm;
        $extrabedprice->subtract_extrabed_price_foreign = $request->subtract_extrabed_price_foreign;
        $extrabedprice->save();

        activity()
            ->performedOn($extrabedprice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Bed Price (Admin Panel)'])
            ->log('New Extra Bed prices are added');

        return redirect()->route('admin.extrabedprices.index')->with('success', 'Successfully Created');
    }

    public function show(ExtraBedPrice $extrabedprice)
    {
        return view('backend.admin.extrabedprices.show', compact('bedtype'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_extra_bed_price')) {
            abort(404);
        }
        $accounttype = AccountType::where('trash', '0')->get();

        $extrabedprice = ExtraBedPrice::findOrFail($id);
        return view('backend.admin.extrabedprices.edit', compact('accounttype', 'extrabedprice'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_extra_bed_price')) {
            abort(404);
        }

        $extrabedprice = ExtraBedPrice::findOrFail($id);
        $extrabedprice->user_account_id = $request['user_id'];
        $extrabedprice->add_extrabed_price_mm = $request->add_extrabed_price_mm;
        $extrabedprice->add_extrabed_price_foreign = $request->add_extrabed_price_foreign;
        $extrabedprice->subtract_extrabed_price_mm = $request->subtract_extrabed_price_mm;
        $extrabedprice->subtract_extrabed_price_foreign = $request->subtract_extrabed_price_foreign;

        $extrabedprice->update();

        activity()
            ->performedOn($extrabedprice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Bed Price (Admin Panel)'])
            ->log(' Extra Bed prices are updated');

        return redirect()->route('admin.extrabedprices.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ExtraBedPrice $extrabedprice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_extra_bed_price')) {
            abort(404);
        }

        $extrabedprice->delete();
        activity()
            ->performedOn($extrabedprice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Bed Price (Admin Panel)'])
            ->log(' Extra Bed prices are deleted');

        return ResponseHelper::success();
    }

    public function trash(ExtraBedPrice $extrabedprice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_extra_bed_price')) {
            abort(404);
        }

        $extrabedprice->trash();
        activity()
            ->performedOn($extrabedprice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Bed Price (Admin Panel)'])
            ->log(' Extra Bed prices are moved to trash');

        return ResponseHelper::success();
    }

    public function restore(ExtraBedPrice $extrabedprice)
    {

        $extrabedprice->restore();
        activity()
            ->performedOn($extrabedprice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Bed Price (Admin Panel)'])
            ->log(' Extra Bed prices are restored from trash');

        return ResponseHelper::success();
    }
}
