<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomFormRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\BedType;
use App\Models\Rooms;
use App\Models\RoomType;
use App\Models\showGallery;
use Illuminate\Http\Request;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;
use Yajra\DataTables\DataTables;

class RoomsController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room')) {
            abort(404);
        }

        if ($request->ajax()) {
            $data = Rooms::anyTrash($request->trash)->with('bedtype', 'roomtype')->orderBy('id', 'desc');
            return Datatables::of($data)
                ->editColumn('facilities', function ($room) {
                    $app = config('app.facilities');
                    $facilities = $room->facilities ? unserialize($room->facilities) : [];
                    $output = '';
                    foreach ($facilities as $facility) {
                        $output .= '<span class="badge badge-info">' . $app[$facility] . '</span>';
                    }
                    return $output;
                })
                ->editColumn('image', function ($room) {
                    return '<img src="' . $room->image_path() . '" width="100px;"/>';
                })
                ->addColumn('roomtype', function ($room) {
                    return $room->roomtype ? $room->roomtype->name : '-';
                })
                ->filterColumn('roomtype', function ($query, $keyword) {
                    $query->whereHas('roomtype', function ($q1) use ($keyword) {
                        $q1->where('name', 'LIKE', "%{$keyword}%");

                    });
                })
                ->addColumn('price', function ($room) {
                    $room_price_mm = $room->price ? $room->price : '0';
                    $room_price_foreign = $room->foreign_price ? $room->foreign_price : '0';

                    return '<ul class="list-group">
                            <li class="list-group-item">' . $room_price_mm . '-(MMK)</li>
                            <li class="list-group-item">' . $room_price_foreign . ' - (USD)</li>
                            </ul>';

                })
                ->addColumn('bedtype', function ($room) {
                    return $room->bedtype ? $room->bedtype->name : '-';
                })
                ->filterColumn('bedtype', function ($query, $keyword) {
                    $query->whereHas('bedtype', function ($q1) use ($keyword) {
                        $q1->where('name', 'LIKE', "%{$keyword}%");

                    });
                })
                ->addColumn('action', function ($room) use ($request) {
                    $restore_btn = '';
                    $detail_btn = '';
                    $edit_btn = '';
                    $upload_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('view_room')) {
                        $detail_btn = '<a class="detail text text-primary" href="' . route('admin.rooms.detail', ['room' => $room->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('edit_room')) {
                        $upload_btn = '<a class="upload text text-primary" href="' . url('admin/rooms/gallery/show', $room->id) . '"><i class="far fa-image fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('edit_room')) {
                        $edit_btn = '<a class="edit text text-primary" href="' . route('admin.rooms.edit', ['room' => $room->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_room')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning" href="#" data-id="' . $room->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger" href="#" data-id="' . $room->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger" href="#" data-id="' . $room->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn} ${upload_btn}";
                })
                ->addColumn('plus-icon', function ($role) {
                    return null;
                })
                ->rawColumns(['facilities', 'image', 'price', 'action'])
                ->make(true);
        }
        return view('backend.admin.rooms.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room')) {
            abort(404);
        }

        $facilities = config('app.facilities');
        $bedtype = BedType::where('trash', 0)->get();
        $roomtype = RoomType::where('trash', 0)->get();
        return view('backend.admin.rooms.create', compact('roomtype', 'bedtype', 'facilities'));
    }

    public function store(RoomFormRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room')) {
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

        $serializedarr = serialize($request->facilities);

        $room = new Rooms();
        $room->room_type = $request['room_type'];
        $room->bed_type = $request['bed_type'];
        $room->price = $request['price'];
        $room->foreign_price = $request['foreign_price'];
        $room->room_qty = $request['room_qty'];
        $room->adult_qty = $request['adult_qty'];
        $room->extra_bed_qty = $request['extra_bed_qty'];
        $room->extra_bed_mm_price = $request['extra_bed_mm_price'];
        $room->extra_bed_foreign_price = $request['extra_bed_foreign_price'];
        $room->early_checkin_mm = $request['early_checkin_mm'];
        $room->early_checkin_foreign = $request['early_checkin_foreign'];
        $room->late_checkout_mm = $request['late_checkout_mm'];
        $room->late_checkout_foreign = $request['late_checkout_foreign'];
        $room->image = $image_name;
        $room->description = $request['description'];
        $room->facilities = $serializedarr;
        $room->autoconfirm = $request['autoconfirm'];
        $room->save();

        activity()
            ->performedOn($room)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Rooms (Admin Panel)'])
            ->log(' New Room is created');

        return redirect()->route('admin.rooms.index')->with(['success' => 'Successfully Created']);

    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room')) {
            abort(404);
        }

        $facilities = config('app.facilities');
        $bedtype = BedType::where('trash', 0)->get();
        $roomtype = RoomType::where('trash', 0)->get();
        $room = Rooms::findOrFail($id);
        $old_facilities = $room->facilities ? collect(unserialize($room->facilities)) : [];

        return view('backend.admin.rooms.edit', compact('roomtype', 'room', 'bedtype', 'facilities', 'old_facilities'));
    }

    public function update(Rooms $room, RoomUpdateRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room')) {
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

        } else {
            $image_name = $room->image;
        }

        $room->room_type = $request->room_type;
        $room->bed_type = $request->bed_type;
        $room->price = $request->price;
        $room->foreign_price = $request->foreign_price;
        $room->room_qty = $request->room_qty;
        $room->adult_qty = $request->adult_qty;
        $room->extra_bed_qty = $request['extra_bed_qty'];
        $room->extra_bed_mm_price = $request['extra_bed_mm_price'];
        $room->extra_bed_foreign_price = $request['extra_bed_foreign_price'];
        $room->early_checkin_mm = $request['early_checkin_mm'];
        $room->early_checkin_foreign = $request['early_checkin_foreign'];
        $room->late_checkout_mm = $request['late_checkout_mm'];
        $room->late_checkout_foreign = $request['late_checkout_foreign'];
        $room->description = $request->description;
        $room->image = $image_name;
        $room->facilities = $request->facilities ? serialize($request->facilities) : null;
        $room->autoconfirm = $request['autoconfirm'];

        $room->update();

        activity()
            ->performedOn($room)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Rooms (Admin Panel)'])
            ->log(' Room is updated');

        return redirect()->route('admin.rooms.index')->with(['success' => 'Successfully Updated']);
    }

    public function delete($id, Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room')) {
            abort(404);
        }
        Rooms::find($id)->delete();

        activity()
            ->performedOn($room)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Rooms (Admin Panel)'])
            ->log(' Room is deleted');

        return redirect()->back()->with(['success' => 'Room have been Deleted']);
    }

    public function galleryShow(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room')) {
            abort(404);
        }

        $id = $request['id'];
        return view('backend.admin.rooms.galleryShow', compact('id'));
    }

    public function galleryUpload(Request $request, $id)
    {
        switch ($request->method()) {
            case 'POST':
                $this->upload_file($id, $request->except('_token'));
                break;

            case 'DELETE':
                $this->delete_uploaded_file($id, $request->except('_token'));
                break;

            default:
                $files = showGallery::where('rooms_id', $id)->get();

                $count = 0;
                $obj = array();
                foreach ($files as $file) {
                    $obj[$count]['id'] = $file->id;
                    $obj[$count]['name'] = 'File - ' . $file->id;
                    $obj[$count]['file'] = $file->image_path();
                    $obj[$count]['size'] = Storage::size('uploads/galleryimage/' . $file->file);
                    $count++;
                }
                return response()->json($obj);
                break;
        }
    }

    public function upload_file($id, array $input)
    {
        $image_file = $input['file'];
        $image_name = time() . '_' . uniqid() . '.' . $image_file->getClientOriginalExtension();
        Storage::put(
            'uploads/galleryimage/' . $image_name,
            file_get_contents($image_file->getRealPath())
        );
        $file = new showGallery();
        $file->rooms_id = $id;
        $file->file = $image_name;
        $file->save();
        return true;
    }

    public function delete_uploaded_file($id, array $input)
    {
        $file = showGallery::find($id)->file;
        Storage::delete('uploads/galleryimage/' . $file);
        return showGallery::find($id)->delete();
    }

    public function get_uploaded_file($id)
    {
        return showGallery::where('rooms_id', $id)->get();
    }

    public function destroy(Rooms $room)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room')) {
            abort(404);
        }
        if ($room->image) {
            $image_file = $room->image;
            Storage::delete('uploads/gallery/' . $image_file);
        }

        $room->delete();
        activity()
            ->performedOn($room)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Rooms (Admin Panel)'])
            ->log(' Room is deleted');

        return ResponseHelper::success();
    }

    public function trash(Rooms $room)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room')) {
            abort(404);
        }

        $room->trash();
        activity()
            ->performedOn($room)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Rooms (Admin Panel)'])
            ->log(' Room is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(Rooms $room)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room')) {
            abort(404);
        }

        $room->restore();
        activity()
            ->performedOn($room)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Rooms (Admin Panel)'])
            ->log(' Room is restored from trash');

        return ResponseHelper::success();
    }

    public function detail($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room')) {
            abort(404);
        }

        $room_detail = Rooms::with('bedtype', 'roomtype', 'roomlayout')->where('id', $id)->first();
        $app = config('app.facilities');
        $facilities = $room_detail->facilities ? unserialize($room_detail->facilities) : [];

        return view('backend.admin.rooms.detail', compact('room_detail', 'facilities', 'app'));
    }
}
