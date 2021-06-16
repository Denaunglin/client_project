<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\Tax;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
                    if ($this->getCurrentAuthUser('admin')->can('edit_tax')) {
                        $edit_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.taxes.edit', ['tax' => $row->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    return "${edit_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);

        }

        return view('backend.admin.tax.index');
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

}
