<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\AccounttypeRequest;


class ExpenseTypeController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        if ($request->ajax()) {
            $expense_types = ExpenseType::anyTrash($request->trash);
            return Datatables::of($expense_types)
                ->addColumn('action', function ($expense_type) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.expense_types.edit', ['expense_type' => $expense_type->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    

                    if ($this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $expense_type->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $expense_type->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $expense_type->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }
                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.expense_types.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        return view(('backend.admin.expense_types.create'));
    }

    public function store(AccounttypeRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_payment_card')) {
            abort(404);
        }

        $expense_type = new ExpenseType();
        $expense_type->name = $request['name'];
        $expense_type->save();
        activity()
            ->performedOn($expense_type)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type  is created');

        return redirect()->route('admin.expense_types.index')->with('success', 'Successfully Created');
    }

    public function show(ExpenseType $expense_type)
    {
        return view('backend.admin.expense_types.show', compact('expense_type'));
    }

    public function edit($id)
    {
      
        $expense_type = ExpenseType::findOrFail($id);
        return view('backend.admin.expense_types.edit', compact('expense_type'));
    }

    public function update(AccounttypeRequest $request, $id)
    {
        $expense_type = ExpenseType::findOrFail($id);
        $expense_type->name = $request['name'];
        $expense_type->update();

        activity()
            ->performedOn($expense_type)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type is updated ');

        return redirect()->route('admin.expense_types.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ExpenseType $expense_type)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }
        $expense_type->delete();
        activity()
            ->performedOn($expense_type)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ExpenseType $expense_type)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }

        $expense_type->trash();
        activity()
            ->performedOn($expense_type)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ExpenseType $expense_type)
    {
        $expense_type->restore();
        activity()
            ->performedOn($expense_type)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type is restored from trash ');

        return ResponseHelper::success();
    }
}
