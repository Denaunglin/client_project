<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Expense;
use App\Models\Cashbook;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

use App\Helper\ResponseHelper;
use App\Models\ExpenseCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\ExpenseRequest;

class ExpenseController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $expense_type = ExpenseType::where('trash',0)->get();
        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $expenses = Expense::anyTrash($request->trash);
            if ($daterange) {
                $expenses = Expense::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            if($request->expense_type){
                $expenses = Expense::where('expense_type_id',$request->expense_type)->get();
            }
            return Datatables::of($expenses)
                ->addColumn('action', function ($expense) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.expenses.edit', ['expense' => $expense->id]) . '"><i class="far fa-edit fa-lg"></i></a>';

                    if ($this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $expense->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $expense->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $expense->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }
                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('expense_category_id', function ($expense) {
                    return $expense->expense_category->name;
                })
                ->addColumn('expense_type_id', function ($expense) {
                    return $expense->expense_type->name;
                })
                ->addColumn('price', function ($expense) {
                    return $expense->price;
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.expenses.index',compact('item','expense_type'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        $expense_categories = ExpenseCategory::where('trash',0)->get();
        $expense_types = ExpenseType::where('trash',0)->get();
        return view('backend.admin.expenses.create',compact('expense_categories','expense_types'));
    }

    public function store(ExpenseRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_payment_card')) {
            abort(404);
        }

        $expense = new Expense();
        $expense->expense_category_id = $request['expense_category_id'];
        $expense->expense_type_id = $request['expense_type_id'];
        $expense->about = $request['about'];
        $expense->price = $request['price'];    
        $expense->save();


        $cash_book = new Cashbook();
        $cash_book->cashbook_income = 0;
        $cash_book->cashbook_outgoing = $expense->price ;
        $cash_book->buying_id = null;
        $cash_book->service_id = null;
        $cash_book->selling_id = null;
        $cash_book->expense_id = $expense->id;
        $cash_book->credit_id = null;
        $cash_book->return_id = null;
        $cash_book->save();

        activity()
            ->performedOn($expense)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense  (Admin Panel)'])
            ->log('Expense  is created');

        return redirect()->route('admin.expenses.index')->with('success', 'Successfully Created');
    }

    public function show(Expense $expense)
    {
        return view('backend.admin.expenses.show', compact('expense'));
    }

    public function edit($id)
    {
      
        $expense = Expense::findOrFail($id);
        $expense_category = ExpenseCategory::where('trash',0)->get();
        $expense_type = ExpenseType::where('trash',0)->get();
        return view('backend.admin.expenses.edit', compact('expense','expense_category','expense_type'));
    }

    public function update(ExpenseRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('update_payment_card')) {
            abort(404);
        }
        $expense = Expense::findOrFail($id);
        $expense->expense_category_id = $request['expense_category_id'];
        $expense->expense_type_id = $request['expense_type_id'];
        $expense->about = $request['about'];
        $expense->price = $request['price'];
        $expense->update();


        $cash_book =  Cashbook::where('expense_id', $expense->id)->first();;
        $cash_book->cashbook_income = 0;
        $cash_book->cashbook_outgoing = $expense->price ;
        $cash_book->buying_id = null;
        $cash_book->selling_id = null;
        $cash_book->service_id = null;
        $cash_book->expense_id = $expense->id;
        $cash_book->credit_id = null;
        $cash_book->return_id = null;
        $cash_book->update();

        activity()
            ->performedOn($expense)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense  (Admin Panel)'])
            ->log('Expense  is updated ');

        return redirect()->route('admin.expenses.index')->with('success', 'Successfully Updated');
    }

    public function destroy(Expense $expense)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }
        $expense->delete();
        activity()
            ->performedOn($expense)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type is deleted ');

        return ResponseHelper::success();
    }

    public function trash(Expense $expense)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }

        $expense->trash();
        activity()
            ->performedOn($expense)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(Expense $expense)
    {
        $expense->restore();
        activity()
            ->performedOn($expense)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Expense Type (Admin Panel)'])
            ->log('Expense Type is restored from trash ');

        return ResponseHelper::success();
    }
}
