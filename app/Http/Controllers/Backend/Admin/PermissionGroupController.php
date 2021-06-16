<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupPermission;
use App\Http\Traits\AuthorizePerson;
use App\Models\GroupPermission;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PermissionGroupController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_permission')) {
            abort(404);
        }

        if ($request->ajax()) {
            $permissiongroups = GroupPermission::where('guard_name', $request->guard)->get();

            return DataTables::of($permissiongroups)
                ->editColumn('rank', function ($permissiongroup) {
                    return $permissiongroup->rank ?? 'xxxx';
                })
                ->addColumn('permissions', function ($permissiongroup) use ($request) {
                    $output = '<ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">New Permission <a href="' . route('admin.permission.create') . '?permission_group_id=' . $permissiongroup->id . '&permission_group_name=' . $permissiongroup->name . '&guard=' . $request->guard . '" class="text-decoration-none"><i class="fas fa-plus-circle text-primary"></i></a></li>';
                    foreach ($permissiongroup->permissions as $permission) {
                        $output .= '<li class="list-group-item d-flex justify-content-between align-items-center">' . $permission->name . '<span><a href="' . route('admin.permission.edit', $permission->id) . '?permission_group_id=' . $permission->permissiongroup_id . '&permission_group_name=' . $permissiongroup->name . '&guard=' . $request->guard . '" class="text-decoration-none mr-2"><i class="far fa-edit text-warning"></i></a> <a href="#" class="permission-delete" data-id="' . $permission->id . '"><i class="far fa-trash-alt text-danger"></i></a></span></li>';
                    }
                    return $output . '</ul>';
                })
                ->addColumn('action', function ($permissiongroup) use ($request) {
                    $edit_icon = "";
                    $delete_icon = "";

                    if ($request->guard == 'admin') {
                        if ($this->getCurrentAuthUser('admin')->can('edit_permission')) {
                            $edit_icon = "<a title='Edit' href='" . route('admin.permission-group.edit', $permissiongroup->id) . "?guard=" . $request->guard . "'><i class='far fa-edit text-warning'></i></a>";
                        }
                        if ($this->getCurrentAuthUser('admin')->can('delete_permission')) {
                            $delete_icon = "<a title='Delete' href='#' class='delete' data-id='" . $permissiongroup->id . "'><i class='far fa-trash-alt text-danger'></i></a>";
                        }
                    } else {
                        if ($this->getCurrentAuthUser('admin')->can('edit_customer_permission')) {
                            $edit_icon = "<a title='Edit' href='" . route('admin.permission-group.edit', $permissiongroup->id) . "?guard=" . $request->guard . "''><i class='far fa-edit text-warning'></i></a>";
                        }

                        if ($this->getCurrentAuthUser('admin')->can('delete_customer_permission')) {
                            $delete_icon = "<a title='Delete' href='#' class='delete' data-id='" . $permissiongroup->id . "'><i class='far fa-trash-alt text-danger'></i></a>";
                        }
                    }

                    return "<div class='action'>
                        " . $edit_icon . "
                        " . $delete_icon . "
                    </div>";

                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }

        return view('backend.admin.permission_group.index');
    }

    public function create(Request $request)
    {
        if ($request->guard == 'admin') {
            if (!$this->getCurrentAuthUser('admin')->can('add_permission')) {
                abort(404);
            }
        } else {
            if (!$this->getCurrentAuthUser('admin')->can('add_customer_permission')) {
                abort(404);
            }
        }

        return view('backend.admin.permission_group.create');
    }

    public function store(StoreGroupPermission $request)
    {
        if ($request->guard == 'admin') {
            if (!$this->getCurrentAuthUser('admin')->can('add_permission')) {
                abort(404);
            }
        } else {
            if (!$this->getCurrentAuthUser('admin')->can('add_customer_permission')) {
                abort(404);
            }
        }

        $permission_group = new GroupPermission();
        $permission_group->rank = $request->rank;
        $permission_group->name = $request->name;
        $permission_group->guard_name = $request->guard;
        $permission_group->save();

        activity()
            ->performedOn($permission_group)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Permission Group (Admin Panel)'])
            ->log('New permission group is added');

        return redirect('/admin/permission-group?guard=' . $request->guard)->with('success', 'Successfully Created.');
    }

    public function edit(Request $request, GroupPermission $permission_group)
    {
        if ($request->guard == 'admin') {
            if (!$this->getCurrentAuthUser('admin')->can('edit_permission')) {
                abort(404);
            }
        } else {
            if (!$this->getCurrentAuthUser('admin')->can('edit_customer_permission')) {
                abort(404);
            }
        }

        return view('backend.admin.permission_group.edit', compact('permission_group'));
    }

    public function update(StoreGroupPermission $request, GroupPermission $permission_group)
    {
        if ($request->guard == 'admin') {
            if (!$this->getCurrentAuthUser('admin')->can('edit_permission')) {
                abort(404);
            }
        } else {
            if (!$this->getCurrentAuthUser('admin')->can('edit_customer_permission')) {
                abort(404);
            }
        }

        $permission_group->rank = $request->rank;
        $permission_group->name = $request->name;
        $permission_group->update();

        activity()
            ->performedOn($permission_group)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Permission Group (Admin Panel)'])
            ->log('Permission group is updated');

        return redirect('/admin/permission-group?guard=' . $request->guard)->with('success', 'Successfully Updated.');
    }

    public function destroy(GroupPermission $permission_group, Request $request)
    {
        if ($request->guard == 'admin') {
            if (!$this->getCurrentAuthUser('admin')->can('delete_permission')) {
                abort(404);
            }

            $roles = Role::where('guard_name', $request->guard)->get();
            foreach ($permission_group->permissions as $permission) {
                foreach ($roles as $role) {
                    if ($role->hasPermissionTo($permission->name)) {
                        $role->revokePermissionTo($permission->name);
                    }
                }
            }
        } else {
            if (!$this->getCurrentAuthUser('admin')->can('delete_customer_permission')) {
                abort(404);
            }

            $roles = Role::where('guard_name', 'web')->get();
            foreach ($permission_group->permissions as $permission) {
                foreach ($roles as $role) {
                    if ($role->hasPermissionTo($permission->name)) {
                        $role->revokePermissionTo($permission->name);
                    }
                }
            }
        }

        $permission_group->delete();
        activity()
            ->performedOn($permission_group)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Permission Group (Admin Panel)'])
            ->log('Permission group is deleted');

        return 'success';
    }
}
