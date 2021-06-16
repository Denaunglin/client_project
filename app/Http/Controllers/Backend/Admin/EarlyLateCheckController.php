<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\EarlyLateCheck;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EarlyLateCheckController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_earlylatecheck')) {
            abort(404);
        }

        if ($request->ajax()) {
            $earlylatechecks = EarlyLateCheck::anyTrash($request->trash)->with('accounttype')->orderBy('id', 'desc');
            return Datatables::of($earlylatechecks)
                ->addColumn('action', function ($earlylatecheck) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_extra_bed_price')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.earlylatechecks.edit', ['earlylatecheck' => $earlylatecheck->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_extra_bed_price')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $earlylatecheck->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $earlylatecheck->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $earlylatecheck->id . '"><i class="fas fa-trash fa-lg"></i></a>';

                        }
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('add_early_checkin', function ($earlylatecheck) {
                    $early_checkin_mm = $earlylatecheck->add_early_checkin_mm ? $earlylatecheck->add_early_checkin_mm : '0';
                    $early_checkin_foreign = $earlylatecheck->add_early_checkin_foreign ? $earlylatecheck->add_early_checkin_foreign : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">MM - ' . $early_checkin_mm . '</li>
                            <li class="list-group-item">Foreign - ' . $early_checkin_foreign . '</li>
                            </ul>';

                })
                ->addColumn('add_late_checkout', function ($earlylatecheck) {
                    $late_checkout_mm = $earlylatecheck->add_late_checkout_mm ? $earlylatecheck->add_late_checkout_mm : '0';
                    $late_checkout_foreign = $earlylatecheck->add_late_checkout_foreign ? $earlylatecheck->add_late_checkout_foreign : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">MM - ' . $late_checkout_mm . '</li>
                            <li class="list-group-item">Foreign - ' . $late_checkout_foreign . '</li>
                            </ul>';

                })
                ->addColumn('subtract_early_checkin', function ($earlylatecheck) {
                    $early_checkin_mm = $earlylatecheck->subtract_early_checkin_mm ? $earlylatecheck->subtract_early_checkin_mm : '0';
                    $early_checkin_foreign = $earlylatecheck->subtract_early_checkin_foreign ? $earlylatecheck->subtract_early_checkin_foreign : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">MM - ' . $early_checkin_mm . '</li>
                            <li class="list-group-item">Foreign - ' . $early_checkin_foreign . '</li>
                            </ul>';

                })
                ->addColumn('subtract_late_checkout', function ($earlylatecheck) {
                    $late_checkout_mm = $earlylatecheck->subtract_late_checkout_mm ? $earlylatecheck->subtract_late_checkout_mm : '0';
                    $late_checkout_foreign = $earlylatecheck->subtract_late_checkout_foreign ? $earlylatecheck->subtract_late_checkout_foreign : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">MM - ' . $late_checkout_mm . '</li>
                            <li class="list-group-item">Foreign - ' . $late_checkout_foreign . '</li>
                            </ul>';

                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action', 'add_early_checkin', 'add_late_checkout', 'subtract_early_checkin', 'subtract_late_checkout'])
                ->make(true);
        }
        return view('backend.admin.earlylatechecks.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_earlylatecheck')) {
            abort(404);
        }
        $accounttype = AccountType::where('trash', '0')->get();

        return view('backend.admin.earlylatechecks.create', compact('accounttype'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_earlylatecheck')) {
            abort(404);
        }
        $earlylatechecks = new EarlyLateCheck();
        $earlylatechecks->user_account_id = $request['user_id'];
        $earlylatechecks->add_early_checkin_mm = $request->add_early_checkin_mm;
        $earlylatechecks->add_early_checkin_foreign = $request->add_early_checkin_foreign;
        $earlylatechecks->add_late_checkout_mm = $request->add_late_checkout_mm;
        $earlylatechecks->add_late_checkout_foreign = $request->add_late_checkout_foreign;
        $earlylatechecks->subtract_early_checkin_mm = $request->subtract_early_checkin_mm;
        $earlylatechecks->subtract_early_checkin_foreign = $request->subtract_early_checkin_foreign;
        $earlylatechecks->subtract_late_checkout_mm = $request->subtract_late_checkout_mm;
        $earlylatechecks->subtract_late_checkout_foreign = $request->subtract_late_checkout_foreign;

        $earlylatechecks->save();
        activity()
            ->performedOn($earlylatechecks)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Early Check-in / Late Check-out (Admin Panel)'])
            ->log('New Early Check-in / Late Check-out prices are added');

        return redirect()->route('admin.earlylatechecks.index')->with('success', 'Successfully Created');
    }

    public function show(EarlyLateCheck $ealrylatecheck)
    {
        return view('backend.admin.ealrylatechecks.show', compact('ealrylatecheck'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_earlylatecheck')) {
            abort(404);
        }
        $accounttype = AccountType::where('trash', '0')->get();
        $earlylatecheck = EarlyLateCheck::findOrFail($id);

        return view('backend.admin.earlylatechecks.edit', compact('accounttype', 'earlylatecheck'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_earlylatecheck')) {
            abort(404);
        }

        $earlylatechecks = EarlyLateCheck::findOrFail($id);
        $earlylatechecks->user_account_id = $request['user_id'];
        $earlylatechecks->add_early_checkin_mm = $request->add_early_checkin_mm;
        $earlylatechecks->add_early_checkin_foreign = $request->add_early_checkin_foreign;
        $earlylatechecks->add_late_checkout_mm = $request->add_late_checkout_mm;
        $earlylatechecks->add_late_checkout_foreign = $request->add_late_checkout_foreign;
        $earlylatechecks->subtract_early_checkin_mm = $request->subtract_early_checkin_mm;
        $earlylatechecks->subtract_early_checkin_foreign = $request->subtract_early_checkin_foreign;
        $earlylatechecks->subtract_late_checkout_mm = $request->subtract_late_checkout_mm;
        $earlylatechecks->subtract_late_checkout_foreign = $request->subtract_late_checkout_foreign;
        $earlylatechecks->update();

        activity()
            ->performedOn($earlylatechecks)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Early Check-in / Late Check-out (Admin Panel)'])
            ->log(' Early Check-in / Late Check-out prices are updated');

        return redirect()->route('admin.earlylatechecks.index')->with('success', 'Successfully Updated');
    }

    public function destroy(EarlyLateCheck $earlylatecheck)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_earlylatecheck')) {
            abort(404);
        }

        $earlylatecheck->delete();
        activity()
            ->performedOn($earlylatechecks)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Early Check-in / Late Check-out (Admin Panel)'])
            ->log('Early Check-in / Late Check-out prices are deleted');

        return ResponseHelper::success();
    }

    public function trash(EarlyLateCheck $earlylatecheck)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_earlylatecheck')) {
            abort(404);
        }

        $earlylatecheck->trash();
        activity()
            ->performedOn($earlylatechecks)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Early Check-in / Late Check-out (Admin Panel)'])
            ->log(' Early Check-in / Late Check-out prices are moved to trash');

        return ResponseHelper::success();
    }

    public function restore(EarlyLateCheck $earlylatecheck)
    {

        $earlylatecheck->restore();
        activity()
            ->performedOn($earlylatechecks)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Early Check-in / Late Check-out (Admin Panel)'])
            ->log(' Early Check-in / Late Check-out prices are restored from trash');

        return ResponseHelper::success();
    }
}
