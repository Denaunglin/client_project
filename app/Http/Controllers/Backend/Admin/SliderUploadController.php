<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\SliderUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Yajra\DataTables\DataTables;

class SliderUploadController extends Controller
{

    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_slider')) {
            abort(404);
        }

        if ($request->ajax()) {
            $sliders = SliderUpload::anyTrash($request->trash);
            return Datatables::of($sliders)
                ->addColumn('action', function ($sliders) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_slider')) {
                        $edit_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.slider_upload.edit', ['slider_upload' => $sliders->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_slider')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-3" href="#" data-id="' . $sliders->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-3" href="#" data-id="' . $sliders->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-3" href="#" data-id="' . $sliders->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('slider_image', function ($sliders) {
                    return '<img src="' . $sliders->image_path() . '" width="100px;"/>';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action', 'message', 'slider_image'])
                ->make(true);
        }
        return view('backend.admin.slider_upload.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_slider')) {
            abort(404);
        }

        return view('backend.admin.slider_upload.create');
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_slider')) {
            abort(404);
        }

        if ($request->hasFile('slider_image')) {
            $image_file = $request->file('slider_image');
            $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/slider/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );
            $file_path = public_path('storage/uploads/slider/' . $image_name);

            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);
        }

        $slider = new SliderUpload();
        $slider->slider_image = $image_name;
        $slider->save();

        activity()
            ->performedOn($slider)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Slider (Admin Panel)'])
            ->log('New Silder is added');

        return redirect()->route('admin.slider_upload.index')->with('success', 'Successfully Created');
    }

    public function show(SliderUpload $slider_uploads)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_slider')) {
            abort(404);
        }
        $sliders = SliderUpload::where('trash', 0)->get();

        return view('backend.admin.slider_upload.show');
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_slider')) {
            abort(404);
        }
        $sliders = SliderUpload::where('trash', 0)->where('id', $id)->first();

        return view('backend.admin.slider_upload.edit', compact('sliders'));
    }

    public function update(Request $request, $id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_slider')) {
            abort(404);
        }
        $sliders = SliderUpload::findOrFail($id);

        if ($request->hasFile('slider_image')) {
            $image_file = $request->file('slider_image');
            $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/slider/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );
            $file_path = public_path('storage/uploads/slider/' . $image_name);

            // $optimizerChain = OptimizerChainFactory::create();
            // $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name = $sliders->slider_image;
        }

        $sliders->slider_image = $image_name;
        $sliders->update();

        activity()
            ->performedOn($sliders)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Slider (Admin Panel)'])
            ->log(' Silder is updated');

        return redirect()->route('admin.slider_upload.index')->with('success', 'Successfully Updated');
    }

    public function destroy($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_slider')) {
            abort(404);
        }
        $slider = SliderUpload::where('id', $id)->first();

        if ($slider->slider_image) {
            $image_file = $slider->slider_image;
            Storage::delete('uploads/slider/' . $image_file);
        }

        $slider->delete();
        activity()
            ->performedOn($slider)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Slider (Admin Panel)'])
            ->log(' Silder is deleted');

        return ResponseHelper::success();
    }

    public function trash(SliderUpload $slider_uploads)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_slider')) {
            abort(404);
        }

        $slider_uploads->trash();
        activity()
            ->performedOn($slider_uploads)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Slider (Admin Panel)'])
            ->log(' Silder is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(SliderUpload $slider_uploads)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_slider')) {
            abort(404);
        }

        $slider_uploads->restore();
        activity()
            ->performedOn($slider_uploads)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Slider (Admin Panel)'])
            ->log(' Silder is restored from trash');

        return ResponseHelper::success();
    }

}
