<?php

namespace App\Repositories\Interfaces;

interface PermissionsRepositoryInterface
{
    /**
     * إرجاع كل الأدوار مع إمكانية البحث والتصفح
     */
    public function getAllPermissions($search = null, $rowsPerPage = 10, $page = 1);
    public function all();
    public function create(array $data);
    public function update($permissionId, array $data);
    public function updateRolePermissions($roleId, array $permissionIds);
    public function softDeletePermissions(array $permissionIds);
    public function delete($permissionId);
}
