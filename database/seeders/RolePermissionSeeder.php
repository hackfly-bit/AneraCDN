<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'upload files',
            'download files',
            'delete files',
            'manage all files',
            'view file activities',
            'manage users',
            'view dashboard stats',
            'create api keys',
            'view api keys',
            'update api keys',
            'delete api keys',
            'manage api keys'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo([
            'upload files',
            'download files',
            'create api keys',
            'view api keys',
            'update api keys',
            'delete api keys'
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@laravel-cdn.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now()
            ]
        );
        $admin->assignRole('admin');

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@laravel-cdn.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now()
            ]
        );
        $user->assignRole('user');

        $this->command->info('Roles, permissions, and default users created successfully!');
    }
}
