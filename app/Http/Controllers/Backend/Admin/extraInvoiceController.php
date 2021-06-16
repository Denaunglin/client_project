<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\Booking;
use App\Models\ExtraInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class extraInvoiceController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_extra_invoice')) {
            abort(404);
        }

        if ($request->ajax()) {

            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $extrainvoices = ExtraInvoice::anyTrash($request->trash)->orderBy('id', 'desc')->with('booking');

            if ($daterange) {
                $extrainvoices = $extrainvoices->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            return Datatables::of($extrainvoices)
                ->addColumn('action', function ($extrainvoice) use ($request) {
                    $restore_btn = '';
                    $detail_btn = '';
                    $trash_or_delete_btn = '';
                    if ($this->getCurrentAuthUser('admin')->can('view_extra_invoice')) {
                        $detail_btn = '<a class="detail text text-primary" href="' . route('admin.extrainvoices.detail', ['extrainvoice' => $extrainvoice->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_extra_invoice')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $extrainvoice->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $extrainvoice->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';

                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $extrainvoice->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.extrainvoices.index');
    }

    public function destroy(ExtraInvoice $extrainvoice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_extra_invoice')) {
            abort(404);
        }
        $file = $extrainvoice->invoice_file;
        Storage::delete('uploads/pdf/' . $file);
        $extrainvoice->delete();
        activity()
            ->performedOn($extrainvoice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Invoice (Admin Panel)'])
            ->log(' Extra Invoice is deleted');

        return ResponseHelper::success();
    }

    public function trash(ExtraInvoice $extrainvoice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_extra_invoice')) {
            abort(404);
        }
        $extrainvoice->trash();
        activity()
            ->performedOn($extrainvoice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Invoice (Admin Panel)'])
            ->log(' Extra Invoice is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(ExtraInvoice $extrainvoice)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_extra_invoice')) {
            abort(404);
        }
        $extrainvoice->restore();
        activity()
            ->performedOn($extrainvoice)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Extra Invoice (Admin Panel)'])
            ->log(' Extra Invoice is restored from trash');

        return ResponseHelper::success();
    }

    public function detail($id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('view_extra_invoice')) {
            abort(404);
        }
        $extrainvoice = ExtraInvoice::with('booking')->where('id', $id)->first();
        $booking = Booking::where('id', $extrainvoice->booking_id)->first();
        $nationality = config('app.nationality');
        $pay_method = config('app.pay_method');
        if ($booking->nationality == 1) {
            $sign1 = "";
            $sign2 = "MMK";
        } else {
            $sign1 = "$";
            $sign2 = "";
        }
        return view('backend.admin.extrainvoices.detail', compact('extrainvoice', 'booking', 'nationality', 'pay_method', 'sign1', 'sign2'));
    }

}
