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
        Cache::forget('all_permissions');

        $permission = Permission::create(['name' => $data['name'], 'guard_name' => $data['guard_name'] ?? 'web']);

        return $permission;
    }

    public function update($permissionId, array $data)
    {
        Cache::forget('all_permissions');

        $permission = Permission::find($permissionId);

        $permission->update($data);
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
        Cache::forget('all_permissions');
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        Cache::flush();
    }

    public function delete($permissionId)
    {
        Cache::forget('all_permissions');
        $permission = Permission::findOrFail($permissionId);
        $permission->delete();
        Cache::flush();
    }
}
