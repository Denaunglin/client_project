<?php

namespace App\Http\Controllers\Backend\Admin;

use Storage;
use Illuminate\Http\Request;
use App\Models\Bussinessinfo;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\BussinessInfoRequest;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class BussinessInfoController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_item')) {
            abort(404);
        }
       
        if ($request->ajax()) {

            $bussiness_infos = Bussinessinfo::anyTrash($request->trash);

            return Datatables::of($bussiness_infos)
                ->addColumn('action', function ($bussiness_info) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.bussiness_infos.edit', ['bussiness_info' => $bussiness_info->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $bussiness_info->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $bussiness_info->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $bussiness_info->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
     
                ->addColumn('logo', function ($bussiness_info) {
                    return '<img src="' . $bussiness_info->image_path() . '" width="100px;"/>';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','logo'])
                ->make(true);
        }
        return view('backend.admin.bussiness_infos.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        return view('backend.admin.bussiness_infos.create');
    }

    public function store(BussinessInfoRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        if ($request->hasFile('image')) {
            $image_file = $request->file('image');
            $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );

            $file_path = public_path('storage/uploads/gallery/' . $image_name);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        }
        
        

        $bussiness_info = new BussinessInfo();
        $bussiness_info->name = $request['name'];
        $bussiness_info->logo = $image_name ?? null;
        $bussiness_info->type = $request['type'];
        $bussiness_info->email = $request['email'];
        $bussiness_info->phone = $request['phone'];
        $bussiness_info->address = $request['address'];
        $bussiness_info->remark = $request['remark'];
        $bussiness_info->save();

        activity()
            ->performedOn($bussiness_info)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bussiness Info (Admin Panel'])
            ->log('Bussiness Info is created ');

        return redirect()->route('admin.bussiness_infos.index')->with('success', 'Successfully Created');
    }

    public function show(BussinessInfo $bussiness_info)
    {
        return view('backend.admin.bussiness_infos.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $bussiness_infos = BussinessInfo::findOrFail($id);

        return view('backend.admin.bussiness_infos.edit', compact('bussiness_infos'));
    }

    public function update(BussinessInfoRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $bussiness_info = BussinessInfo::findOrFail($id);

        if ($request->hasFile('image')) {
            $image_file = $request->file('image');
            $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );

            $file_path = public_path('storage/uploads/gallery/' . $image_name);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name = $bussiness_info->image;
        }

        $bussiness_info->name = $request['name'];
        $bussiness_info->logo = $image_name ?? null;
        $bussiness_info->type = $request['type'];
        $bussiness_info->phone = $request['phone'];
        $bussiness_info->email = $request['email'];
        $bussiness_info->address = $request['address'];
        $bussiness_info->remark = $request['remark'];
        $bussiness_info->update();

        activity()
            ->performedOn($bussiness_info)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bussiness Info (Admin Panel'])
            ->log('Bussiness Info is updated');

        return redirect()->route('admin.bussiness_infos.index')->with('success', 'Successfully Updated');
    }

    public function destroy(BussinessInfo $bussiness_info)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $bussiness_info->delete();
        activity()
            ->performedOn($bussiness_info)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Bussiness Info (Admin Panel'])
            ->log(' Bussiness Info  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(BussinessInfo $bussiness_info)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $bussiness_info->trash();
        activity()
            ->performedOn($bussiness_info)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bussiness Info  (Admin Panel)'])
            ->log(' Bussiness Info is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(BussinessInfo $bussiness_info)
    {
        $bussiness_info->restore();
        activity()
            ->performedOn($bussiness_info)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bussiness Info  (Admin Panel'])
            ->log(' Bussiness Info is restored from trash ');

        return ResponseHelper::success();
    }
}
