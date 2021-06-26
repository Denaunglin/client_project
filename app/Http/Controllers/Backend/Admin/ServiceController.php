<?php
namespace App\Http\Controllers\Backend\Admin;


use App\Models\Service;
use App\Models\Cashbook;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\ServiceRequest;


class ServiceController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('view_item')) {
            abort(404);
        }      
        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;

            $services = Service::anyTrash($request->trash);
            if ($daterange) {
                $services = Service::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            return Datatables::of($services)
                ->addColumn('action', function ($service) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    $invoice_btn = '<a class="edit text text-primary" href="' . route('admin.service_invoices', $service->id) . '"><i class="fas fa-file-invoice-dollar fa-lg"></i></a>';

                    $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.services.edit', ['service' => $service->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    
                    if ($request->trash == 1) {
                        $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $service->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                        $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $service->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                    } else {
                        $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $service->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn} ${invoice_btn}";
                })
              
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->addColumn('description', function ($services) {
                    return '<p style="white-space:pre-wrap;">'.$services->description.'</p>';
                })
                ->rawColumns(['action','description'])
                ->make(true);
        }
        return view('backend.admin.services.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
      
        return view('backend.admin.services.create');
    }

    public function store(ServiceRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

      
        $services = new Service();
        $services->service_name = $request['service_name'];
        $services->service_charges = $request['service_charges'];
        $services->description = $request['description'];
        $services->save();

        
        $cash_book = new Cashbook();
        $cash_book->cashbook_income = $services->service_charges ;
        $cash_book->cashbook_outgoing = 0 ;
        $cash_book->buying_id = null;
        $cash_book->service_id= $services->id;
        $cash_book->selling_id = null;
        $cash_book->expense_id = null;
        $cash_book->credit_id = null;
        $cash_book->return_id = null;
        $cash_book->save();

        activity()
            ->performedOn($services)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' New Item  (' . $services->service_name . ') is created ');

        return redirect()->route('admin.services.index')->with('success', 'Successfully Created');
    }

    public function show(Service $service)
    {
        return view('backend.admin.services.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $services = Service::findOrFail($id);


        return view('backend.admin.services.edit', compact('services'));
    }

    public function update(ServiceRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $services = Service::findOrFail($id);

        $services->service_name = $request['service_name'];
        $services->service_charges = $request['service_charges'];
        $services->description = $request['description'];
        $services->update();

        $cash_book = Cashbook::where('service_id',$services->id)->first();
        $cash_book->cashbook_income = $services->service_charges ;
        $cash_book->cashbook_outgoing = 0 ;
        $cash_book->buying_id = null;
        $cash_book->service_id= $services->id;
        $cash_book->selling_id = null;
        $cash_book->expense_id = null;
        $cash_book->credit_id = null;
        $cash_book->return_id = null;
        $cash_book->save();

        activity()
            ->performedOn($services)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log('Item  (' . $services->service_name . ') is updated');

        return redirect()->route('admin.services.index')->with('success', 'Successfully Updated');
    }

    public function destroy(Service $service)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $service->delete();
        activity()
            ->performedOn($service)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $service->service_name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(Service $service)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $service->trash();
        activity()
            ->performedOn($service)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel)'])
            ->log(' Item (' . $service->service_name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(Service $service)
    {
        $service->restore();
        activity()
            ->performedOn($service)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $service->service_name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
    
}
