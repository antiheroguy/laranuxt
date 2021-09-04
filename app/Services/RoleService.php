<?php

namespace App\Services;

use App\Models\Role;
use DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleService extends BaseService
{
    protected $filters = [
        'name' => ['name', '='],
        'name_like' => ['name', 'like'],
        'not' => ['name', '!='],
    ];

    /**
     * @return Role
     */
    public function getModel()
    {
        return Role::class;
    }

    /**
     * @return void
     */
    public function clean(Role $role)
    {
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();
        $role->syncPermissions();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Get permission list.
     *
     * @return Permission
     */
    public function getPermissions()
    {
        return Permission::all();
    }
}
