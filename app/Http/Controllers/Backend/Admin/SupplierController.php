<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\SupplierRequest;


class SupplierController extends Controller
{    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_user')) {
            abort(404);
        }
        if ($request->ajax()) {

            $suppliers = Supplier::anyTrash($request->trash)->orderBy('id', 'desc');
            return DataTables::of($suppliers)

                ->addColumn('action', function ($supplier) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $detail_btn = '';
                    $trash_or_delete_btn = '';
                    if ($this->getCurrentAuthUser('admin')->can('edit_user')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.suppliers.edit', ['supplier' => $supplier->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('delete_user')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $supplier->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $supplier->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $supplier->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    if ($this->getCurrentAuthUser('admin')->can('view_user')) {
                        $detail_btn = '<a class="detail text text-primary" href="' . route('admin.suppliers.detail', ['supplier' => $supplier->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
            
                ->addColumn('plus-icon', function () {
                    return null;
                })

                ->rawColumns(['roles', 'action', 'plus-icon'])
                ->make(true);
        }

        return view('backend.admin.suppliers.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user')) {
            abort(404);
        }
        return view('backend.admin.suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user')) {
            abort(404);
        }

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->phone = $request->phone;
        $supplier->save();

        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Supplier '])
            ->log('New Supplier is added');

        return redirect()->route('admin.suppliers.index')->with('success', 'New User Successfully Created.');
    }

    public function show(Supplier $supplier)
    {
        if (!$this->getCurrentAuthUser('admin')->can('show_user')) {
            abort(404);
        }
        return view('backend.admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_user')) {
            abort(404);
        }

        $suppliers = $supplier;
        return view('backend.admin.suppliers.edit', compact('suppliers'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_user')) {
            abort(404);
        }

        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->phone = $request->phone;
        $supplier->save();

        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Supplier'])
            ->log('Supplier is updated');

        return redirect()->route('admin.suppliers.index')->with('success', 'Successfully Updated.');
    }

    public function destroy(Supplier $supplier)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user')) {
            abort(404);
        }

        $supplier->delete();
        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Supplier'])
            ->log('Supplier is deleted');

        return ResponseHelper::success();
    }

    public function trash(Supplier $supplier)
    {
        $supplier->trash();
        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Supplier'])
            ->log('Supplier is moved to trash');
        return ResponseHelper::success();
    }

    public function restore(Supplier $supplier)
    {
        $supplier->restore();
        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Supplier'])
            ->log('Supplier is restored from trash');

        return ResponseHelper::success();
    }

    public function detail($id)
    {
       $supplier = Supplier::where('id',$id)->first();
        return view('backend.admin.suppliers.detail', compact('supplier'));
    }
}
