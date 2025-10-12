<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الصلاحيات الافتراضية
        $permissions = [
            'create users',
            'view users',
            'edit users',
            'delete users',

            'create partners',
            'view partners',
            'edit partners',
            'delete partners',

            'create orders',
            'view orders',
            'edit orders',
            'delete orders',
        ];

        // إنشاء كل الصلاحيات إن لم تكن موجودة
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إنشاء الأدوار
        $roles = [
            'admin' => [
                'create users', 'view users', 'edit users', 'delete users',
                'create partners', 'view partners', 'edit partners', 'delete partners',
                'create orders', 'view orders', 'edit orders', 'delete orders',
            ],
            'partner' => [
                'view users',
                'create orders', 'view orders', 'edit orders',
            ],
            'user' => [
                'view orders',
                'create orders',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('✅ Roles and permissions created successfully.');
    }
}
