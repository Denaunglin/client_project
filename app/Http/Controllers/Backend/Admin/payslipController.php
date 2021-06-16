<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\Booking;
use App\Models\Payslip;
use App\Models\RoomSchedule;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Yajra\DataTables\DataTables;

class payslipController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payslip')) {
            abort(404);
        }
        $trash = $request->trash ?? 0;
        $payslips = Payslip::anyTrash($trash)->orderBy('id', 'desc');
        $payslips_filter = $payslips->get()->unique('booking_no');
        $config_status = config('app.status');

        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            if ($daterange) {
                $payslips = Payslip::anyTrash($trash)->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            if ($request->booking_number != '') {
                $payslips = $payslips->where('booking_no', $request->booking_number);
            }

            return Datatables::of($payslips)
                ->addIndexColumn()
                ->addColumn('Payslip', function ($payslips) use ($request, $config_status) {
                    $class_name = $payslips->read_at ? '' : 'bg-light';
                    $detail_btn = '';
                    $trash_or_delete_btn = '';
                    $restore_btn = '';
                    $change_status_btn = '';

                    $status_color = "badge-warning";
                    if ($payslips->status == 1) {
                        $status_color = "badge-success";
                    } elseif ($payslips->status == 2) {
                        $status_color = "badge-danger";
                    } elseif ($payslips->status == 3) {
                        $status_color = "badge-success";
                    }

                    if ($this->getCurrentAuthUser('admin')->can('edit_payslip')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.payslips.edit', ['payslip' => $payslips->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('view_payslip')) {
                        $detail_btn = '<a class="edit text text-primary" href="' . route('admin.payslips.detail', ['payslip' => $payslips->id]) . '"><i class="far fa-file fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('view_payslip')) {
                        $change_status_btn = '<a href="#" class=" text-light btn btn-primary change_status mb-1" data-id="' . $payslips->id . '" data-status="' . $payslips->status . '" data-remark="' . $payslips->remark . '"> Change Status</a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_payslip')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $payslips->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $payslips->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $payslips->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    $markasread_icon = '';
                    if (!$payslips->read_at) {
                        $markasread_icon = '<a href="#" title="Mark as read" class="markasread" data-id="' . $payslips->id . '"><i class="far fa-envelope-open text-info"></i></a>';
                    }

                    return '<div class="list border-left ' . $class_name . '">
                                <div class="row">
                                    <div class="col-md-3">
                                     <div class="d-flex w-100 justify-content-between">
                                            <img src="' . $payslips->image_path() . '" width="20%" height="20%">
                                            <h5 class="mb-1 m-2"> ' . $payslips->booking_no . '</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <p class="mb-1 text-muted">' . $payslips->created_at->format("Y-M-d") . ' (' . Carbon::parse($payslips->created_at)->diffForHumans() . ')</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <p class="mb-1 text-muted"><span class="badge ' . $status_color . ' ">' . $config_status[$payslips->status] . '</span></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                    <div class="action">' . $change_status_btn . ' ' . $markasread_icon . '  ' . $detail_btn . '' . $edit_btn . '' . $restore_btn . '' . $trash_or_delete_btn . ' </div>
                                    </div>
                                </div>
                            </div>';
                })

                ->addColumn('plus-icon', function () {
                    return null;
                })

                ->rawColumns(['Payslip'])
                ->make(true);
        }
        return view('backend.admin.payslips.index', compact('payslips_filter'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_payslip')) {
            abort(404);
        }
        $booking = Booking::where('trash', 0)->where('payslip_id', null)->get();

        return view('backend.admin.payslips.create', compact('booking'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_payslip')) {
            abort(404);
        }

        if ($request->hasFile('payslip_image')) {
            $image_file = $request->file('payslip_image');
            $image_name = time() . '_' . uniqid() . $request->booking_no . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/payslip/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );
            $file_path = public_path('storage/uploads/payslip/' . $image_name);

            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);
        }

        $payslip = new Payslip();
        $payslip->booking_no = $request['booking_no'];
        $payslip->payslip_image = $image_name;
        $payslip->remark = $request->remark;
        $payslip->save();

        $booking = Booking::where('booking_number', $request->booking_no)->first();
        $booking->payslip_id = $payslip->id;
        $booking->update();

        activity()
            ->performedOn($payslip)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Payslip (Admin Panel)'])
            ->log(' New Payslip is created');

        return redirect()->route('admin.payslips.index')->with('success', 'Successfully Created');
    }

    public function show(Payslip $payslip)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_payslip')) {
            abort(404);
        }

        if (!$payslip->read_at) {
            $payslip->read_at = date('Y-m-d H:i:s');
            $payslip->update();
        }
        $booking = Booking::where('booking_number', $payslip->booking_no)->first();
        $takeroom = null;
        $sign1 = null;
        $sign2 = null;
        $commission_percentage = null;
        $commission = null;
        if ($booking) {
            $takeroom = RoomSchedule::where('booking_id', $booking->id)->get();
            if ($booking->nationality == 1) {
                $sign1 = '';
                $sign2 = 'MMK';
            } else {
                $sign1 = '$';
                $sign2 = '';
            }
            if ($booking->commission) {
                $commission_percentage = $booking->commission_percentage;
                $commission = $booking->commission;
            }
        }
        $nationality = config('app.nationality');
        $pay_method = config('app.pay_method');

        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        $commission = 0;
        $commission_percentage = 0;

        return view('backend.admin.payslips.show', compact('payslip', 'booking', 'takeroom', 'nationality', 'pay_method', 'sign1', 'sign2', 'service_percentage', 'commission', 'commission_percentage', 'commercial_percentage'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_payslip')) {
            abort(404);
        }

        $booking = Booking::where('trash', 0)->get();
        $payslip = Payslip::findOrFail($id);
        $config_status = config('app.status');

        return view('backend.admin.payslips.edit', compact('payslip', 'booking', 'config_status'));
    }

    public function update(Request $request, $id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_payslip')) {
            abort(404);
        }
        $payslip = Payslip::findOrFail($id);

        if ($request->hasFile('payslip_image')) {
            $image_file = $request->file('payslip_image');
            $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/payslip/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );
            $file_path = public_path('storage/uploads/payslip/' . $image_name);

            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name = $payslip->payslip_image;
        }

        $payslip->booking_no = $request->booking_no;
        $payslip->payslip_image = $image_name;
        $payslip->remark = $request->remark;
        $payslip->status = $request->status;
        $payslip->update();

        activity()
            ->performedOn($payslip)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Payslip (Admin Panel)'])
            ->log(' Payslip is updated');

        return redirect()->route('admin.payslips.index')->with('success', 'Successfully Updated');
    }

    public function destroy(Payslip $payslip)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payslip')) {
            abort(404);
        }
        $payslip_file = $payslip->payslip_image;
        Storage::delete('uploads/payslip/' . $payslip_file);
        $payslip->delete();

        activity()
            ->performedOn($payslip)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Payslip (Admin Panel)'])
            ->log('Payslip is deleted');

        return ResponseHelper::success();
    }

    public function trash(Payslip $payslip)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payslip')) {
            abort(404);
        }

        $payslip->trash();
        activity()
            ->performedOn($payslip)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Payslip (Admin Panel)'])
            ->log('  Payslip is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(Payslip $payslip)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_payslip')) {
            abort(404);
        }

        $payslip->restore();
        activity()
            ->performedOn($payslip)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Payslip (Admin Panel)'])
            ->log('  Payslip is restored from trash');

        return ResponseHelper::success();
    }

    public function markasread($id)
    {
        $payslip = Payslip::where('id', $id)->first();

        if ($payslip) {
            if (!$payslip->read_at) {
                $payslip->read_at = Carbon::now()->format('Y-m-d');
                $payslip->update();
            }

            return ResponseHelper::success();
        }
        return ResponseHelper::failedMessage('Payslip Not Found');

    }
    public function change_status(Request $request, $id)
    {
        $payslip = Payslip::where('id', $id)->first();
        $payslip->remark = $request->remark ? $request->remark : $payslip->remark;
        $payslip->status = $request->status;
        $payslip->update();
        return ResponseHelper::success();

    }

}
