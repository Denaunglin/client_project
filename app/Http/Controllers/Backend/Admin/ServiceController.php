<?php
namespace App\Http\Controllers\Backend\Admin;


use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;

class ServiceController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('view_item')) {
            abort(404);
        }      
        if ($request->ajax()) {

            $services = Service::anyTrash($request->trash);

            return Datatables::of($services)
                ->addColumn('action', function ($service) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.services.edit', ['service' => $service->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $service->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $service->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $service->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.services.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
      
        return view('backend.admin.buying_items.create');
    }

    public function store(ItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

      
        $services = new Service();
        $services->service_name = $request['service_name'];
        $services->retail_price = $request['retail_price'];
        $services->retail_discount = $request['retail_discountunit'];
        $services->retail_tax = $request['retail_tax'];
        $services->item_sub_category_id = $request['item_sub_category_id'];
        $services->wholesale_price = $request['wholesale_price'];
        $services->wholesale_discount = $request['wholesale_discount'];
        $services->wholesale_tax = $request['wholesale_tax'];
        $services->save();

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


        return view('backend.admin.buying_items.edit', compact('services'));
    }

    public function update(ItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $services = Service::findOrFail($id);

        $services->service_name = $request['service_name'];
        $services->retail_price = $request['retail_price'];
        $services->retail_discount = $request['retail_discountunit'];
        $services->retail_tax = $request['retail_tax'];
        $services->item_sub_category_id = $request['item_sub_category_id'];
        $services->wholesale_price = $request['wholesale_price'];
        $services->wholesale_discount = $request['wholesale_discount'];
        $services->wholesale_tax = $request['wholesale_tax'];
        $services->update();

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
