<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccounttypeRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AccountTypeController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_account_type')) {
            abort(404);
        }

        if ($request->ajax()) {
            $accounttypes = AccountType::anyTrash($request->trash);
            return Datatables::of($accounttypes)
                ->addColumn('action', function ($accounttype) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';
                    $restore_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_account_type')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.accounttypes.edit', ['accounttype' => $accounttype->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_account_type')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $accounttype->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $accounttype->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $accounttype->id . '"><i class="fas fa-trash fa-lg"></i></a>';

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
        return view('backend.admin.accounttypes.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_account_type')) {
            abort(404);
        }
        return view(('backend.admin.accounttypes.create'));
    }

    public function store(AccounttypeRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_account_type')) {
            abort(404);
        }
        $accounttype = new AccountType();
        $accounttype->name = $request['name'];
        $accounttype->save();

        activity()
            ->performedOn($accounttype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Account Type (Admin Panel)'])
            ->log(' New Account Type ' . $accounttype->name . ' is created ');

        return redirect()->route('admin.accounttypes.index')->with('success', 'Successfully Created');
    }

    public function show(AccountType $accounttype)
    {
        return view('backend.admin.accounttypes.show', compact('accounttype'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_account_type')) {
            abort(404);
        }

        $accounttype = AccountType::findOrFail($id);
        return view('backend.admin.accounttypes.edit', compact('accounttype'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_account_type')) {
            abort(404);
        }

        $accounttype = AccountType::findOrFail($id);
        $accounttype->name = $request['name'];
        $accounttype->update();

        activity()
            ->performedOn($accounttype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Account Type (Admin Panel)'])
            ->log('Account Type ' . $accounttype->name . ' is updated');

        return redirect()->route('admin.accounttypes.index')->with('success', 'Successfully Updated');
    }

    public function destroy(AccountType $accounttype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_account_type')) {
            abort(404);
        }

        $accounttype->delete();
        activity()
            ->performedOn($accounttype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Account Type (Admin Panel)'])
            ->log('Account Type ' . $accounttype->name . ' is deleted');

        return ResponseHelper::success();
    }

    public function trash(AccountType $accounttype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_account_type')) {
            abort(404);
        }
        $accounttype->trash();

        activity()
            ->performedOn($accounttype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Account Type (Admin Panel)'])
            ->log('Account Type ' . $accounttype->name . ' is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(AccountType $accounttype)
    {
        $accounttype->restore();
        activity()
            ->performedOn($accounttype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Account Type (Admin Panel)'])
            ->log('Account Type ' . $accounttype->name . ' is restored from trash');

        return ResponseHelper::success();
    }
}
