<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AdminRoleRepositoryInterface;
use App\Models\AdminRole;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminRoleRepository implements AdminRoleRepositoryInterface
{
    public function getAllRoles($search = null, $rowsPerPage = 10, $page = 1)
    {
        $cacheKey = "roles_{$search}_{$rowsPerPage}_{$page}";

        return Cache::remember($cacheKey, 60, function () use ($search, $rowsPerPage) {
            $query = AdminRole::query();

            if ($search) {
                $query->where('name', 'LIKE', "%$search%");
            }

            // استثناء دور السوبر أدمن مثلاً
            return $query->where('name', '!=', 'super-admin')->paginate($rowsPerPage);
        });
    }

    public function allRoles()
    {
        return AdminRole::where('name', '!=', 'super-admin')->get();
    }

    public function createRole(array $data)
    {
        $role = AdminRole::create(['name' => $data['name'], 'guard_name' => $data['guard_name'] ?? 'web']);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        Cache::flush();
        return $role;
    }

    public function getRolePermissions($roleId)
    {
        $role = AdminRole::findOrFail($roleId);
        return $role->permissions;
    }

    public function updateRolePermissions($roleId, array $permissionsData)
    {
        $role = AdminRole::findOrFail($roleId);
        $role->syncPermissions($permissionsData);

        Cache::flush();
        return $role->permissions;
    }

    public function updateRole($roleId, array $data)
    {
        $role = AdminRole::findOrFail($roleId);

        if (isset($data['name'])) {
            $role->name = $data['name'];
        }

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        $role->save();

        Cache::flush();
        return $role;
    }

    public function softDeleteRoles(array $roleIds)
    {
        DB::table('roles')->whereIn('id', $roleIds)->delete();
        Cache::flush();
    }

    public function deleteRole($roleId)
    {
        $role = AdminRole::findOrFail($roleId);
        $role->delete();
        Cache::flush();
    }
}
