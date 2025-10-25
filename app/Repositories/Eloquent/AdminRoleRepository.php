<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AdminRoleRepositoryInterface;
use App\Models\AdminRole;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginatesCollection;
use Spatie\Permission\Models\Role;

class AdminRoleRepository implements AdminRoleRepositoryInterface
{
    use PaginatesCollection;

    public function getAllRoles($search = null, $rowsPerPage = 10, $page = 1)
    {
        $cacheKey = "all_roles";

        $items = Cache::remember($cacheKey, 60, function () {
            return Role::orderBy('id', 'desc')->with('permissions')->get();
        });

        if ($search) {
            $items = $items->filter(function ($item) use ($search) {
                return stripos($item->name, $search) !== false;
            });
        }
        return $this->paginate($items, $rowsPerPage, $page);
    }

    public function allRoles()
    {
        return Role::where('name', '!=', 'super-admin')->get();
    }

    public function createRole(array $data)
    {
        Cache::forget('all_roles');

        $role = Role::create(['name' => $data['name'], 'guard_name' => $data['guard_name'] ?? 'web']);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        return $role;
    }


    public function updateRole($roleId, array $data)
    {
        Cache::forget('all_roles');

        $role = Role::find($roleId);

        $role->update($data);
    }

    public function softDeleteRoles(array $roleIds)
    {
        DB::table('roles')->whereIn('id', $roleIds)->delete();
        Cache::flush();
    }

    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();
        Cache::flush();
    }


    public function updateRolePermissions($roleId, array $permissions)
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($permissions);
        Cache::flush();
        return $role;
    }
}
