<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\PermissionsRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginatesCollection;
use Spatie\Permission\Models\Role;

class PermissionsRepository implements PermissionsRepositoryInterface
{
    use PaginatesCollection;
    public function getAllPermissions($search = null, $rowsPerPage = 10, $page = 1)
    {
        $cacheKey = "all_permissions";

        $items = Cache::remember($cacheKey, 60, function () {
            return Permission::orderBy('id', 'desc')->get();
        });

        if ($search) {
            $items = $items->filter(function ($item) use ($search) {
                return stripos($item->name, $search) !== false;
            });
        }
        return $this->paginate($items, $rowsPerPage, $page);
    }

    public function all()
    {
        return Permission::all();
    }

    public function create(array $data)
    {
        return Permission::create($data);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        Cache::flush();
        return $role;
    }

    public function update($permissionId, array $data)
    {
        $permission = Permission::findOrFail($permissionId);

        if (isset($data['name'])) {
            $permission->name = $data['name'];
        }

        if (isset($data['permissions'])) {
            $permission->syncPermissions($data['permissions']);
        }

        $permission->save();

        Cache::flush();
        return $permission;
    }

    public function updateRolePermissions($roleId, array $permissionIds)
    {
        $role = Role::findOrFail($roleId);
        $permissions = Permission::whereIn('id', $permissionIds)->get();

        $role->syncPermissions($permissions);

        Cache::flush();
    }

    public function softDeletePermissions(array $permissionIds)
    {
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        Cache::flush();
    }

    public function delete($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        $permission->delete();
        Cache::flush();
    }
}
