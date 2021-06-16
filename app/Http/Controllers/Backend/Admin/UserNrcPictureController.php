<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserNrcPicRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\User;
use App\Models\UserNrcPicture;
use Illuminate\Http\Request;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;
use Yajra\DataTables\DataTables;

class UserNrcPictureController extends Controller
{

    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_user_nrc_image')) {
            abort(404);
        }
        if ($request->ajax()) {
            $usernrcimages = UserNrcPicture::anyTrash($request->trash)->with('user')->orderBy('id', 'desc');
            return Datatables::of($usernrcimages)
                ->editColumn('front_pic', function ($usernrcimage) {
                    return '<img src="' . $usernrcimage->image_path_front() . '" width="100px;"/>';
                })
                ->editColumn('back_pic', function ($usernrcimage) {
                    return '<img src="' . $usernrcimage->image_path_back() . '" width="100px;"/>';
                })
                ->addColumn('action', function ($usernrcimage) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('view_user_nrc_image')) {
                        $detail_btn = '<a class="text text-primary mr-2" href="' . route('admin.usernrcimages.show', ['usernrcimage' => $usernrcimage->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('edit_user_nrc_image')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.usernrcimages.edit', ['usernrcimage' => $usernrcimage->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_user_nrc_image')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $usernrcimage->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $usernrcimage->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $usernrcimage->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "<div class='action'>${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}</div>";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })

                ->rawColumns(['action', 'front_pic', 'back_pic'])
                ->make(true);
        }
        return view('backend.admin.usernrcimages.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user_nrc_image')) {
            abort(404);
        }
        $user = User::where('trash', 0)->get();

        return view('backend.admin.usernrcimages.create', compact('user'));
    }

    public function store(UserNrcPicRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user_nrc_image')) {
            abort(404);
        }

        if ($request->hasFile('front_pic')) {
            $image_file_front = $request->file('front_pic');
            $image_name_front = time() . '_' . uniqid() . '.' . $image_file_front->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name_front,
                file_get_contents($image_file_front->getRealPath())
            );
            $file_path = public_path('storage/uploads/gallery/' . $image_name_front);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);
        }
        if ($request->hasFile('back_pic')) {
            $image_file_back = $request->file('back_pic');
            $image_name_back = time() . '_' . uniqid() . '.' . $image_file_back->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name_back,
                file_get_contents($image_file_back->getRealPath())
            );
            $file_path = public_path('storage/uploads/gallery/' . $image_name_back);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);
        }

        $usernrcimage = new UserNrcPicture();
        $usernrcimage->user_id = $request['user_id'];
        $usernrcimage->front_pic = $image_name_front;
        $usernrcimage->back_pic = $image_name_back;
        $usernrcimage->save();

        activity()
            ->performedOn($usernrcimage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Nrc Picture (Admin Panel)'])
            ->log(' User Nrc Image is added');

        return redirect()->route('admin.usernrcimages.index')->with('success', 'Successfully Created');
    }

    public function show(UserNrcPicture $usernrcimage)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_user_nrc_image')) {
            abort(404);
        }

        return view('backend.admin.usernrcimages.show', compact('usernrcimage'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_user_nrc_image')) {
            abort(404);
        }
        $user = User::where('trash', 0)->get();

        $usernrcimage = UserNrcPicture::findOrFail($id);
        return view('backend.admin.usernrcimages.edit', compact('usernrcimage', 'user'));
    }

    public function update(Request $request, $id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_user_nrc_image')) {
            abort(404);
        }
        $usernrcimage = UserNrcPicture::findOrFail($id);

        if ($request->hasFile('front_pic')) {
            $image_file_front = $request->file('front_pic');
            $image_name_front = time() . '_' . uniqid() . '.' . $image_file_front->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name_front,
                file_get_contents($image_file_front->getRealPath())
            );
            $file_path = public_path('storage/uploads/gallery/' . $image_name_front);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name_front = $usernrcimage->front_pic;
        }

        if ($request->hasFile('back_pic')) {
            $image_file_back = $request->file('back_pic');
            $image_name_back = time() . '_' . uniqid() . '.' . $image_file_back->getClientOriginalExtension();
            Storage::put(
                'uploads/gallery/' . $image_name_back,
                file_get_contents($image_file_back->getRealPath())
            );
            $file_path = public_path('storage/uploads/gallery/' . $image_name_back);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name_back = $usernrcimage->back_pic;
        }

        $usernrcimage = new UserNrcPicture();
        $usernrcimage->user_id = $request->user_id;
        $usernrcimage->front_pic = $image_name_front;
        $usernrcimage->back_pic = $image_name_back;

        $usernrcimage->save();
        activity()
            ->performedOn($usernrcimage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Nrc Picture (Admin Panel)'])
            ->log(' User Nrc Image is updated');

        return redirect()->route('admin.usernrcimages.index')->with('success', 'Successfully Updated');
    }

    public function destroy(UserNrcPicture $usernrcimage)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user_nrc_image')) {
            abort(404);
        }
        $front_file = $usernrcimage->front_pic;
        $back_file = $usernrcimage->back_pic;
        Storage::delete('uploads/gallery/' . $front_file);
        Storage::delete('uploads/gallery/' . $back_file);
        $usernrcimage->delete();
        activity()
            ->performedOn($usernrcimage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Nrc Picture (Admin Panel)'])
            ->log(' User Nrc Image is deleted');

        return ResponseHelper::success();
    }

    public function trash(UserNrcPicture $usernrcimage)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user_nrc_image')) {
            abort(404);
        }

        $usernrcimage->trash();
        activity()
            ->performedOn($usernrcimage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Nrc Picture (Admin Panel)'])
            ->log(' User Nrc Image is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(UserNrcPicture $usernrcimage)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user_nrc_image')) {
            abort(404);
        }

        $usernrcimage->restore();
        activity()
            ->performedOn($usernrcimage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'User Nrc Picture (Admin Panel)'])
            ->log(' User Nrc Image is restored from trash');

        return ResponseHelper::success();
    }
}
