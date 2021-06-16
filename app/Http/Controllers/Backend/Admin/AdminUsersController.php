<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUser;
use App\Http\Requests\UpdateAdminUser;
use App\Http\Traits\AuthorizePerson;
use App\Models\AdminUser;
use App\Models\Role;
use Hash;
use Illuminate\Http\Request;use Yajra\DataTables\DataTables;

class AdminUsersController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_admin')) {
            abort(404);
        }

        if ($request->ajax()) {
            $admin_users = AdminUser::anyTrash($request->trash);

            return DataTables::of($admin_users)
                ->addColumn('roles', function ($admin_users) {
                    return $admin_users->getRoleNames()->reduce(function ($carry, $each) {
                        return "${carry}<span class='badge badge-primary mr-1'>${each}</span>";
                    });
                })

                ->addColumn('action', function ($admin_user) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';
                    $restore_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_admin')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.admin-users.edit', ['admin_user' => $admin_user->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_admin')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $admin_user->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $admin_user->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $admin_user->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })

                ->addColumn('plus-icon', function () {
                    return null;
                })

                ->rawColumns(['roles', 'action', 'plus-icon'])
                ->make(true);
        }
        return view('backend.admin.admin_users.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_admin')) {
            abort(404);
        }

        $roles = Role::where('guard_name', config('custom_guards.default.admin'))->get();
        return view('backend.admin.admin_users.create', compact('roles'));
    }

    public function store(StoreAdminUser $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('add_admin')) {
            abort(404);
        }
        $created = AdminUser::create
            ([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $created->syncRoles($request->roles);

        return redirect()->route('admin.admin-users.index')->with('success', 'New Admin User Successfully Created.');
    }

    public function show(AdminUser $admin_user)
    {

        if (!$this->getCurrentAuthUser('admin')->can('add_admin')) {
            abort(404);
        }
        return view('backend.admin.admin_users.show', compact('admin_user'));
    }

    public function edit(AdminUser $admin_user)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_admin')) {
            abort(404);
        }
        $roles = Role::where('guard_name', config('custom_guards.default.admin'))->get();
        return view('backend.admin.admin_users.edit', compact('admin_user', 'roles'));

    }

    public function update(UpdateAdminUser $request, AdminUser $admin_user)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_admin')) {
            abort(404);
        }
        $admin_user->name = $request->name;
        $admin_user->email = $request->email;
        if (!empty($request->password)) {
            $admin_user->password = Hash::make($request->password);
        }
        $admin_user->save();
        $admin_user->syncRoles($request->roles);

        return redirect()->route('admin.admin-users.index')->with('success', 'Successfully Updated.');

    }

    public function destroy(AdminUser $admin_user)
    {

        if (!$this->getCurrentAuthUser('admin')->can('delete_admin')) {
            abort(404);
        }
        $admin_user->delete();
        return ResponseHelper::success();

    }

    public function trash(AdminUser $admin_user)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_admin')) {
            abort(404);
        }
        $admin_user->trash();
        return ResponseHelper::success();
    }

    public function restore(AdminUser $admin_user)
    {
        $admin_user->restore();
        return ResponseHelper::success();
    }
}
