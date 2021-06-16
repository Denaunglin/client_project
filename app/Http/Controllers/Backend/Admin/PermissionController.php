<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupPermission;
use App\Http\Traits\AuthorizePerson;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use AuthorizePerson;

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

        return view('backend.admin.permission.create');
    }

    public function store(StoreGroupPermission $request)
    {
        $permission = new Permission();
        $permission->group_id = $request->permission_group_id;
        $permission->name = $request->name;
        $permission->guard_name = $request->guard;
        $permission->save();

        activity()
            ->performedOn($permission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Permission (Admin Panel)'])
            ->log(' New permission is added');

        return redirect('/admin/permission-group?guard=' . $request->guard)->with('success', 'Successfully Created.');
    }

    public function edit(Request $request, Permission $permission)
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

        return view('backend.admin.permission.edit', compact('permission'));
    }

    public function update(StoreGroupPermission $request, Permission $permission)
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

        $permission->name = $request->name;
        $permission->update();
        activity()
            ->performedOn($permission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Permission (Admin Panel)'])
            ->log('Permission is updated');

        return redirect('/admin/permission-group?guard=' . $request->guard)->with('success', 'Successfully Updated.');
    }

    public function destroy(Permission $permission, Request $request)
    {
        if ($request->guard == 'admin') {
            if (!$this->getCurrentAuthUser('admin')->can('delete_permission')) {
                abort(404);
            }
            $roles = Role::where('guard_name', 'admin')->get();
            foreach ($roles as $role) {
                if ($role->hasPermissionTo($permission->name)) {
                    $role->revokePermissionTo($permission->name);
                }
            }
        } else {
            if (!$this->getCurrentAuthUser('admin')->can('delete_customer_permission')) {
                abort(404);
            }

            $roles = Role::where('guard_name', 'customers')->get();
            foreach ($roles as $role) {
                if ($role->hasPermissionTo($permission->name)) {
                    $role->revokePermissionTo($permission->name);
                }
            }
        }

        $permission->delete();
        activity()
            ->performedOn($permission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Permission (Admin Panel)'])
            ->log(' Permission is deleted');

        return 'success';
    }
}
