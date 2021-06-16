<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DepositController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_checkin_deposit')) {
            abort(404);
        }

        if ($request->ajax()) {
            $data = Deposit::all();
            return Datatables::of($data)
                ->addColumn('action', function ($row) use ($request) {
                    $detail_btn = '';
                    $edit_btn = '';
                    if ($this->getCurrentAuthUser('admin')->can('edit_checkin_deposit')) {
                        $edit_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.deposits.edit', ['deposit' => $row->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    return "${edit_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.checkindeposit.index');
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_checkin_deposit')) {
            abort(404);
        }

        $deposit = Deposit::where('id', $id)->firstOrFail();
        return view('backend.admin.checkindeposit.edit', compact('deposit'));
    }

    public function update(DepositRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_checkin_deposit')) {
            abort(404);
        }
        $deposit = Deposit::findOrFail($id);
        $deposit->night = $request['night'];
        $deposit->deposit = $request['deposit'];
        $deposit->update();

        activity()
            ->performedOn($deposit)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Deposite (Admin Panel)'])
            ->log('Deposite is updated ');

        return redirect()->route('admin.deposits.index')->with('success', 'Successfully Updated');
    }
}
