<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Cashbook;
use App\Models\SellItems;
use Illuminate\Http\Request;
use App\Http\Requests\SellItem;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;

class ProfitLostController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        $date = date('Y-m-d');
        $cashbook_data=[];
        $cash_books = Cashbook::where('trash',0)->whereDate('created_at',$date)->get();
        $sell_item = SellItems::where('trash',0)->whereDate('created_at',$date)->get();
        $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
        if ($daterange) {
            $cash_books = Cashbook::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1])->get();
            $sell_item = SellItems::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1])->get();
        }   
        // $cash_books = Cashbook::where('trash',0)->get();
        $total_selling_amount = 0;
        $total_selling_profit = 0;

        foreach($sell_item as $data){
            $total_selling_amount += $data->net_price;
        }

       
        $cashbook_income = 0;
        $cashbook_outgoing = 0;
        $cashbook_income_list = [];
        $cashbook_outgoing_list = [];

        $profit = 0;
        $lost = 0;

        $column= ["one" => '1'];

        $item_wholesale_price = 0;
        $item_retail_price = 0 ;
        $item_buying_price = 0;

        $check_price = [];

        $item_id = [];
        foreach($cash_books as $data){
            $cashbook_income += $data->cashbook_income;
            $cashbook_income_list [] = $data->cashbook_income;
            $cashbook_outgoing += $data->cashbook_outgoing;
            $cashbook_outgoing_list =  $data->cashbook_outgoing;

            if($data->selling_id){
                $item_id  []= $data->selling->item->id ;
            }
        }

        
        $item_count = count ($item_id);
          if($item_count != 0){
              foreach($item_id as $id){
                $item = Item::where('id',$id)->first();
                $item_wholesale_price = $item->wholesale_price ;
                $item_retail_price = $item->retail_price ;
                $item_buying_price = $item->buying_price;

                $check_price [] = [
                    "item_wholesale_price" => $item_wholesale_price,
                    "item_retail_price" => $item_retail_price,
                    "item_buying_price" => $item_buying_price,
                ];
              }
            }
            $total_buy_origin_price = 0;
            $total_retail_price = 0;
            $total_wholesale_price = 0;

            foreach($check_price as $data){
                $total_buy_origin_price += $data['item_buying_price'];
                $total_retail_price += $data['item_retail_price'] ;
                $total_wholesale_price += $data['item_wholesale_price'] ;

            }
        
        $total_selling_profit =  ($total_selling_amount ) - $total_buy_origin_price ;

        $cashbook_total = $cashbook_income + $cashbook_outgoing;
        if($cashbook_income > $cashbook_outgoing){
            $profit = $cashbook_income - $cashbook_outgoing;
        }
        if($cashbook_outgoing > $cashbook_income){
            $lost = $cashbook_outgoing - $cashbook_income;
        }

        $cashbook_data = [
            "total_selling_profit" => number_format($total_selling_profit),
            "total_sell_qty" => number_format($item_count),
            'cashbook_income' => number_format($cashbook_income),
            'cashbook_outgoing' => number_format($cashbook_outgoing),
            'cashbook_total' => number_format($cashbook_total),
            'cashbook_income_list' => $cashbook_income_list,
            'cashbook_outgoing_list' => $cashbook_outgoing_list,
            'profit' =>number_format( $profit),
            'lost' => number_format($lost) ,
        ];

        if ($request->ajax()) {
            return Datatables::of($column)
                ->addColumn('action', function ($column ) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('table', function ($column) use ($cashbook_data) {
                    return '
                        <table class="table table-bordered mb-3">
                            <thead>
                                <tr>
                                <td> Total Income </td>
                                <td> Total Outgoing </td>
                                <td> Total CashBook </td>
                                <td> CashBook Profit </td>
                                <td> CashBook Lost </td>
                                </tr>
                            </thead>
                           
                                <tbody>
                                    <tr class="bg-light">
                                    <td> '.$cashbook_data['cashbook_income'].' MMK </td>
                                    <td> '.$cashbook_data['cashbook_outgoing'].' MMK </td>
                                    <td>  '.$cashbook_data['cashbook_total'].' MMK </td>
                                    <td>  '.$cashbook_data['profit'].' MMK </td>
                                    <td>  '.$cashbook_data['lost'].' MMK </td>
                                    </tr>
                                </tfoot>
                            </tbody>

                            <table class="table table-bordered">
                            <thead>
                                <tr>
                                <td> Total Selling Qty </td>
                                <td> Total Selling Profit </td>
                                </tr>
                            </thead>
                           
                                <tbody>
                                    <tr class="bg-light">
                                    <td> '.$cashbook_data['total_sell_qty'].'  </td>
                                    <td> '.$cashbook_data['total_selling_profit'].' MMK </td>
                                    </tr>
                                </tfoot>
                            </tbody>
                    
                    ' ;
                })
                
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','table'])
                ->make(true);
        }
        return view('backend.admin.profit_lost.index');
    }
}
