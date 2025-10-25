<?php

namespace App\Repositories\Interfaces;

interface AdminRoleRepositoryInterface
{
    /**
     * إرجاع كل الأدوار مع إمكانية البحث والتصفح
     */
    public function getAllRoles($search = null, $rowsPerPage = 10, $page = 1);
    public function allRoles();
    public function createRole(array $data);
    public function updateRole($roleId, array $data);
    public function updateRolePermissions($roleId, array $permissionIds);
    public function softDeleteRoles(array $roleIds);
    public function deleteRole($roleId);
}
