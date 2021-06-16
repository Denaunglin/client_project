<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CardTypeRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\CardType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CardTypeController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        if ($request->ajax()) {
            $cardtypes = CardType::anyTrash($request->trash);
            return Datatables::of($cardtypes)
                ->addColumn('action', function ($cardtype) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('update_payment_card')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.cardtypes.edit', ['cardtype' => $cardtype->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $cardtype->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $cardtype->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $cardtype->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.cardtypes.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payment_card')) {
            abort(404);
        }
        return view(('backend.admin.cardtypes.create'));
    }

    public function store(CardTypeRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_payment_card')) {
            abort(404);
        }

        $cardtype = new CardType();
        $cardtype->name = $request['name'];
        $cardtype->save();
        activity()
            ->performedOn($cardtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Card Type (Admin Panel)'])
            ->log('Card Type (' . $cardtype->name . ')  is created');

        return redirect()->route('admin.cardtypes.index')->with('success', 'Successfully Created');
    }

    public function show(CardType $cardtype)
    {
        return view('backend.admin.cardtypes.show', compact('cardtype'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('update_payment_card')) {
            abort(404);
        }
        $cardtype = CardType::findOrFail($id);
        return view('backend.admin.cardtypes.edit', compact('cardtype'));
    }

    public function update(CardTypeRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('update_payment_card')) {
            abort(404);
        }
        $cardtype = CardType::findOrFail($id);
        $cardtype->name = $request['name'];
        $cardtype->update();

        activity()
            ->performedOn($cardtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Card Type (Admin Panel)'])
            ->log('Card Type (' . $cardtype->name . ')  is updated ');

        return redirect()->route('admin.cardtypes.index')->with('success', 'Successfully Updated');
    }

    public function destroy(CardType $cardtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }
        $cardtype->delete();
        activity()
            ->performedOn($cardtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Card Type (Admin Panel)'])
            ->log('Card Type (' . $cardtype->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(CardType $cardtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payment_card')) {
            abort(404);
        }

        $cardtype->trash();
        activity()
            ->performedOn($cardtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Card Type (Admin Panel)'])
            ->log('Card Type (' . $cardtype->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(CardType $cardtype)
    {
        $cardtype->restore();
        activity()
            ->performedOn($cardtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Card Type (Admin Panel)'])
            ->log('Card Type (' . $cardtype->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
