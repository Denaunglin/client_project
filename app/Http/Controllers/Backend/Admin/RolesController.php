<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRole;
use App\Http\Requests\UpdateRole;
use App\Http\Traits\AuthorizePerson;
use App\Models\GroupPermission;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RolesController extends Controller
{
    use AuthorizePerson;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view roles')) {
            abort(404);
        }

        if ($request->ajax()) {
            $roles = Role::where('guard_name', $request->guard);

            return DataTables::of($roles)
                ->addColumn('action', function ($role) use ($request) {
                    $detail_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.roles.show', ['role' => $role->id]) . '?guard=' . $request->guard . '"><i class="fas fa-info-circle fa-lg"></i></a>';;
                    $edit_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.roles.edit', ['role' => $role->id]) . '?guard=' . $request->guard . '"><i class="far fa-edit fa-lg"></i></a>';
                    // $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $role->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';

                    return "${detail_btn} ${edit_btn}";
                })

                ->addColumn('plus-icon', function ($role) {
                    return null;
                })

                ->rawColumns(['action', 'plus-icon'])
                ->make(true);
        }

        return view('backend.admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add role')) {
            abort(404);
        }
        $grouppermission = GroupPermission::where('trash', '0')->get();
        $permissions = Permission::where('guard_name', $request->guard)->get();
        return view('backend.admin.roles.create', compact('permissions', 'grouppermission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreRole $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('add role')) {
            abort(404);
        }
        $created = Role::firstOrCreate(['name' => $request->name, 'guard_name' => $request->guard]);

        if ($created) {
            $created->syncPermissions($request->permissions);
            return redirect(config('app.prefix_admin_url') . '/admin/roles?guard=' . $request->guard)->with('success', 'New Role Successfully Created.');
        }
        return back()->with('error', 'New Role Create Failed !')->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Role $role)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add role')) {
            abort(404);
        }
        return view('backend.admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Role $role)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit role')) {
            abort(404);
        }
        $grouppermission = GroupPermission::where('trash', '0')->get();
        $permissions = Permission::where('guard_name', $request->guard)->get();
        return view('backend.admin.roles.edit', compact('role', 'permissions', 'grouppermission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(UpdateRole $request, Role $role)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit role')) {
            abort(404);
        }
        $updated = $role->update
            ([
            'name' => $request->name,
        ]);
        if ($updated) {
            $role->syncPermissions($request->permissions);
            return redirect(config('app.prefix_admin_url') . '/admin/roles?guard=' . $request->guard)->with('success', 'Successfully Updated.');
        }
        return back()->with('error', 'Role Update Failed !')->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {

        // $role->delete();
        // return ResponseHelper::success();
    }
}
