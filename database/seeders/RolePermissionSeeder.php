<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $guestRole = Role::firstOrCreate(['name' => 'wisatawan', 'guard_name' => 'web']);

        // Create default admin if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => 'password', // Laravel 12 casts to hashed
            ]
        );

        if (! $admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        // Sample wisatawan user and profile
        $guest = User::firstOrCreate(
            ['email' => 'guest@example.com'],
            [
                'name' => 'Tamu Contoh',
                'password' => 'password',
            ]
        );

        if (! $guest->hasRole($guestRole)) {
            $guest->assignRole($guestRole);
        }

        $guest->wisatawan()->firstOrCreate([], [
            'name' => $guest->name,
            'no_hp' => '081200000000',
            'nationality' => 'Indonesia',
            'status' => 'active',
        ]);
    }
}
