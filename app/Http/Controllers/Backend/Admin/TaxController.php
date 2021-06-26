<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Http\Requests\TaxRequest;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;

class TaxController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_tax')) {
            abort(404);
        }

        if ($request->ajax()) {

            $data = Tax::all();

            return Datatables::of($data)
                ->addColumn('action', function ($row) use ($request) {
                    $detail_btn = '';
                    $edit_btn = '';
                    $edit_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.taxes.edit', ['tax' => $row->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $row->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';

                    return "${edit_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);

        }

        return view('backend.admin.tax.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user')) {
            abort(404);
        }
        return view('backend.admin.tax.create');
    }

    public function store(Request $request)
    {
       
        $tax = new Tax();
        $tax->name = $request->name;
        $tax->amount = $request->amount;
        $tax->save();

        activity()
            ->performedOn($tax)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'tax '])
            ->log('New tax is added');

        return redirect()->route('admin.taxes.index')->with('success', 'New Tax Successfully Created.');
    }

    public function show(Tax $tax)
    {
        if (!$this->getCurrentAuthUser('admin')->can('show_user')) {
            abort(404);
        }
        return view('backend.admin.tax.show', compact('tax'));
    }


    public function edit($id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_tax')) {
            abort(404);
        }

        $tax = Tax::where('id', $id)->firstOrFail();
        return view('backend.admin.tax.edit', compact('tax'));
    }

    public function update(TaxRequest $request, $id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_tax')) {
            abort(404);
        }

        $tax = Tax::findOrFail($id);
        $tax->amount = $request['amount'];
        $tax->update();

        activity()
            ->performedOn($tax)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Tax (Admin Panel)'])
            ->log(' Tax amount is updated');

        return redirect()->route('admin.taxes.index')->with('success', 'Successfully Updated');

    }

    public function destroy(Tax $tax)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user')) {
            abort(404);
        }

        $tax->delete();
        activity()
            ->performedOn($tax)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Tax'])
            ->log('Tax is deleted');

        return ResponseHelper::success();
    }

}
