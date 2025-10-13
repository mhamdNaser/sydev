<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\AdminRoleRepository;
use Illuminate\Http\Request;
use App\Models\AdminRole;

class AdminRoleController extends Controller
{
    protected $roles;

    public function __construct(AdminRoleRepository $roles)
    {
        $this->roles = $roles;
    }

    public function index(Request $request)
    {
        $roles = $this->roles->getAllRoles($request->search);
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = $this->roles->createRole($data);
        return response()->json($role);
    }

    public function show($id)
    {
        return response()->json(AdminRole::findOrFail($id));
    }

    public function permission($id)
    {
        $permissions = $this->roles->getRolePermissions($id);
        return response()->json($permissions);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'permissions']);
        $role = $this->roles->updateRole($id, $data);
        return response()->json($role);
    }

    public function deleteRoleArray(Request $request)
    {
        $this->roles->softDeleteRoles($request->role_ids);
        return response()->json(['message' => 'Roles deleted successfully']);
    }

    public function destroy($id)
    {
        $this->roles->deleteRole($id);
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
