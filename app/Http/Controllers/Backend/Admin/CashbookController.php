<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Cashbook;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;


class CashbookController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
            
        if ($request->ajax()){

            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $cash_books = Cashbook::with('item')->anyTrash($request->trash);
            if ($daterange) {
                $cash_books = Cashbook::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            return Datatables::of($cash_books)
                ->addColumn('action', function ($cashbook) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                        // $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.cash_books.edit', ['cashbook' => $cashbook->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $cashbook->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $cashbook->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $cashbook->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('status', function ($cashbook) {
                    $status = "";
                    if($cashbook->service_id != null){
                        $status= '<span class=" badge badge-success text-white"> Service Charges </span>';
                    }
                    if($cashbook->buying_id != null){
                        $status= '<span class=" badge badge-danger text-white"> Buying Charges </span>';

                    }
                    if($cashbook->selling_id != null){
                        $status= '<span class=" badge badge-success text-white"> Sale Charges </span>';

                    }
                    if($cashbook->opening_id != null){
                        $status= '<span class=" badge badge-danger text-white"> Opening Charges </span>';

                    }
                    if($cashbook->expense_id != null){
                        $status= '<span class=" badge badge-danger text-white"> Expense Charges </span>';

                    }
                    if($cashbook->credit_id != null){
                        $status= '<span class=" badge badge-warning text-white"> Credit Charges </span>';

                    }
                    if($cashbook->return_id != null){
                        $status= '<span class=" badge badge-success text-white"> Return Charges </span>';
                    }
                    return $status ;
                })
                
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('backend.admin.cash_books.index');
    }

    public function destroy(CashBook $cash_book){
        $cash_book->delete();
        return ResponseHelper::success();

    }

}
