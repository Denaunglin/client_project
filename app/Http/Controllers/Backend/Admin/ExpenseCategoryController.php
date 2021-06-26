<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\CardType;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ExpenseCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\CardTypeRequest;
use App\Http\Requests\AccounttypeRequest;


class ExpenseCategoryController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        if ($request->ajax()) {
            $expense_categories = ExpenseCategory::anyTrash($request->trash);
            return Datatables::of($expense_categories)
                ->addColumn('action', function ($expense_category) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.expense_categories.edit', ['expense_category' => $expense_category->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    

                    if ($this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $expense_category->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $expense_category->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $expense_category->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.expense_categories.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        return view(('backend.admin.expense_categories.create'));
    }

    public function store(AccounttypeRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_payment_card')) {
            abort(404);
        }

        $expense_category = new ExpenseCategory();
        $expense_category->name = $request['name'];
        $expense_category->save();
        activity()
            ->performedOn($expense_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Category (Admin Panel)'])
            ->log('Expense Category  is created');

        return redirect()->route('admin.expense_categories.index')->with('success', 'Successfully Created');
    }

    public function show(ExpenseCategory $expense_category)
    {
        return view('backend.admin.expense_categories.show', compact('expense_category'));
    }

    public function edit($id)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('update_payment_card')) {
        //     abort(404);
        // }

        $expense_category = ExpenseCategory::findOrFail($id);
        return view('backend.admin.expense_categories.edit', compact('expense_category'));
    }

    public function update(AccounttypeRequest $request, $id)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('update_payment_card')) {
        //     abort(404);
        // }
        $expense_category = ExpenseCategory::findOrFail($id);
        $expense_category->name = $request['name'];
        $expense_category->update();

        activity()
            ->performedOn($expense_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Category (Admin Panel)'])
            ->log('Expense Category is updated ');

        return redirect()->route('admin.expense_categories.index')->with('success', 'Successfully Updated');
    }

    public function destroy(CardType $expense_category)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }
        $expense_category->delete();
        activity()
            ->performedOn($expense_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Category (Admin Panel)'])
            ->log('Expense Category is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ExpenseCategory $expense_category)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }

        $expense_category->trash();
        activity()
            ->performedOn($expense_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Category (Admin Panel)'])
            ->log('Expense Category  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ExpenseCategory $expense_category)
    {
        $expense_category->restore();
        activity()
            ->performedOn($expense_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Category (Admin Panel)'])
            ->log('Expense Category is restored from trash ');

        return ResponseHelper::success();
    }
}
