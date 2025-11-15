<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $roles = [
            'owner',
            'admin_produksi',
            'sales',
            'user',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $users = [
            [
                'name' => 'Owner',
                'email' => 'owner@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ],
            [
                'name' => 'Admin Produksi',
                'email' => 'produksi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin_produksi',
            ],
            [
                'name' => 'Sales',
                'email' => 'sales@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'sales',
            ],
            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ]
            );

            $user->assignRole($data['role']);
        }
    }
}
