<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientUser;
use App\Http\Requests\UpdateClientUser;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\Role;
use App\Models\User;
use App\Models\User as ClientUser;
use App\Models\UserNrcPicture;
use App\Models\UserProfile;
use Hash;
use Illuminate\Http\Request;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;
use Yajra\DataTables\DataTables;

class ClientUsersController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_user')) {
            abort(404);
        }
        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;

            $client_users = ClientUser::anyTrash($request->trash)->orderBy('id', 'desc');

            if ($daterange) {
                $client_users = $client_users->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            return DataTables::of($client_users)
                ->addColumn('roles', function ($client_user) {
                    return $client_user->getRoleNames()->reduce(function ($carry, $each) {
                        return "${carry}<span class='badge badge-primary mr-1'>${each}</span>";
                    });
                })

                ->addColumn('action', function ($client_user) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $detail_btn = '';
                    $trash_or_delete_btn = '';
                    if ($this->getCurrentAuthUser('admin')->can('edit_user')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.client-users.edit', ['client_user' => $client_user->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('delete_user')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $client_user->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $client_user->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $client_user->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    if ($this->getCurrentAuthUser('admin')->can('view_user')) {
                        $detail_btn = '<a class="detail text text-primary" href="' . route('admin.client-users.detail', ['client_user' => $client_user->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('account_type', function ($client_user) {
                    if ($client_user->accounttype) {
                        $account_type = $client_user->accounttype->name;
                    } else {
                        $account_type = 'Default User';

                    }
                    return "${account_type} ";
                })
                ->filterColumn('account_type', function ($query, $keyword) {
                    $query->whereHas('accounttype', function ($q1) use ($keyword) {
                        $q1->where('name', 'LIKE', "%{$keyword}%");

                    });
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })

                ->rawColumns(['roles', 'action', 'plus-icon'])
                ->make(true);
        }

        return view('backend.admin.client_users.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user')) {
            abort(404);
        }
        $roles = Role::where('guard_name', config('custom_guards.default.user'))->get();
        
        return view('backend.admin.client_users.create', compact('roles'));
    }

    public function store(StoreClientUser $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_user')) {
            abort(404);
        }

        $created = ClientUser::create
            ([
            'name' => $request->name,
            'email' => $request->email ? $request->email : null,
            'phone' => $request->phone,
            'nrc_passport' => $request->nrc_passport ?  $request->nrc_passport : null ,
            'date_of_birth' => $request->date_of_birth ? $request->date_of_birth : null,
            'gender' => $request->gender,
            'address' => $request->address,
            'password' => 0,
            'account_type' => '1',
        ]);
        $created->syncRoles($request->roles);

        activity()
            ->performedOn($created)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Client User (Admin Panel)'])
            ->log('New Client User is added');

        return redirect()->route('admin.client-users.index')->with('success', 'New User Successfully Created.');
    }

    public function show(ClientUser $client_user)
    {
        if (!$this->getCurrentAuthUser('admin')->can('show_user')) {
            abort(404);
        }
        return view('backend.admin.client_users.show', compact('client_user'));
    }

    public function edit(ClientUser $client_user)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_user')) {
            abort(404);
        }

        $accounttype = AccountType::where('trash', '0')->get();
        $roles = Role::where('guard_name', config('custom_guards.default.user'))->get();

        return view('backend.admin.client_users.edit', compact('client_user', 'roles', 'accounttype'));
    }

    public function update(UpdateClientUser $request, ClientUser $client_user)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_user')) {
            abort(404);
        }

        $client_user->name = $request->name;
        $client_user->email =$request->email ? $request->email : null;
        $client_user->phone = $request->phone;
        $client_user->nrc_passport = $request->nrc_passport ? $request->nrc_passport : null;
        $client_user->date_of_birth = $request->date_of_birth;
        $client_user->gender = $request->gender;
        $client_user->address = $request->address;
        $client_user->account_type = $request->account_type;
        $client_user->password = 0;
        $client_user->save();

        $client_user->syncRoles($request->roles);

        activity()
            ->performedOn($client_user)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Client User (Admin Panel)'])
            ->log('Client User is updated');

        return redirect()->route('admin.client-users.index')->with('success', 'Successfully Updated.');
    }

    public function destroy(ClientUser $client_user)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_user')) {
            abort(404);
        }
       
        if ($client_user->usernrcimage) {
            $front_file = $client_user->usernrcimage->front_pic;
            $back_file = $client_user->usernrcimage->back_pic;
            Storage::delete('uploads/gallery/' . $front_file);
            Storage::delete('uploads/gallery/' . $back_file);
            $client_user->usernrcimage->delete();
        }

        $client_user->delete();
        activity()
            ->performedOn($client_user)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Client User (Admin Panel)'])
            ->log('Client User is deleted');

        return ResponseHelper::success();
    }

    public function trash(ClientUser $client_user)
    {
        $client_user->trash();
        activity()
            ->performedOn($client_user)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Client User (Admin Panel)'])
            ->log('Client User is moved to trash');
        return ResponseHelper::success();
    }

    public function restore(ClientUser $client_user)
    {
        $client_user->restore();
        activity()
            ->performedOn($client_user)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Client User (Admin Panel)'])
            ->log('Client User is restored from trash');

        return ResponseHelper::success();
    }

    public function detail($id)
    {
        $client_detail = User::where('id', $id)->first();
        return view('backend.admin.client_users.detail', compact('client_detail'));
    }
}
