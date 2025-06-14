<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'code' => Str::uuid(),
                'role' => 'superadmin',
                'email' => 'eyvaz@proweb.az',
                'password' => 'eyvaz_proweb',
                'name' => 'Eyvaz Jafarov',
            ],
            [
                'code' => Str::uuid(),
                'role' => 'superadmin',
                'email' => 'system@proweb.az',
                'password' => 'system_proweb',
                'name' => 'Proweb System',
            ],
            [
                'code' => Str::uuid(),
                'role' => 'manager',
                'email' => 'manager@proweb.az',
                'password' => 'manager_proweb',
                'name' => 'Proweb Manager',
            ],
            [
                'code' => Str::uuid(),
                'role' => 'person',
                'email' => 'person1@proweb.az',
                'password' => 'person1_proweb',
                'name' => 'Proweb Person',
            ],
        ];

        foreach ($users as $userData) {

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'code' => $userData['code'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            $role = Role::where('name', $userData['role'])->first();
            if ($role)
                $user->assignRole($role);
        }
    }
}
