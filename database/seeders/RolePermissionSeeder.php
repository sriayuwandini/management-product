<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'owner',
            'user',
            'admin_produksi',
            'sales',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view products', 'create products', 'edit products', 'delete products',
            'view sales', 'create sales', 'edit sales', 'delete sales',
            'view reports', 'create reports'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        Role::where('name', 'owner')->first()?->syncPermissions(Permission::all());
        Role::where('name', 'user')->first()?->syncPermissions(['view products']);
        Role::where('name', 'admin_produksi')->first()?->syncPermissions(['view products', 'create products', 'edit products']);
        Role::where('name', 'sales')->first()?->syncPermissions(['view products', 'create sales', 'view sales', 'create sales', 'edit sales']);

    }
}
