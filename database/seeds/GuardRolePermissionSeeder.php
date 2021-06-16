<?php

use App\Models\GroupPermission;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class GuardRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_permission_groups = config('custom_admin_permissions.default');
        $customer_permissions = config('custom_user_permissions.default');

        // $customer_permissions = config('custom_user_permissions.default');

        foreach ($admin_permission_groups as $permission_group) {
            $rank = $permission_group['rank'] ?? null;
            $name = $permission_group['name'];
            $permissions = $permission_group['permissions'];

            $permissiongroup = GroupPermission::firstOrCreate(['name' => $name, 'guard_name' => config('custom_guards.default.admin')], ['rank' => $rank]);
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['group_id' => $permissiongroup->id, 'name' => $permission, 'guard_name' => config('custom_guards.default.admin')]);
            }
        }

        // foreach ($customer_permissions as $customer_permission) {
        //     Permission::firstOrCreate(['name' => $customer_permission, 'guard_name' => config('custom_guards.default.user')]);
        // }

        $roles = config('custom_roles.admin');
        // $customer_roles = config('custom_roles.user');

        foreach ($roles as $role) {
            $role_created = Role::firstOrCreate(['guard_name' => config('custom_guards.default.admin'), 'name' => $role]);
            if ($role_created->name == 'Admin') {
                // assign all permissions
                $role_created->syncPermissions(Permission::where('guard_name', config('custom_guards.default.admin'))->get());
            }
        }

        // foreach ($customer_roles as $customer_role) {
        //     $customer_role_created = Role::firstOrCreate(['guard_name' => config('custom_guards.default.user'), 'name' => $customer_role]);
        //     // assign all permissions
        //     $customer_role_created->syncPermissions(Permission::where('guard_name', config('custom_guards.default.user'))->get());
        // }
    }
}
