<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission_list = [
            'translations',
            'currency_manager',
            'transaction_status_manager',
            'delete_transaction',
            'transactions_manager',
            'account_manager',
            'atm_bank_note_manager',
            'admin_manager',
            'users_manager',
        ];

        $role_list = [
            'superadmin',
            'manager',
            'person'
        ];

        // Create permissions
        foreach ($permission_list as $permission_name) {
            Permission::firstOrCreate(['name' => $permission_name]);
        }

        // Create roles and assign all permissions to superadmin
        foreach ($role_list as $role_name) {
            $role = Role::firstOrCreate(['name' => $role_name]);

            if ($role_name === 'superadmin') {
                $role->syncPermissions($permission_list);
            }

            if ($role_name === 'manager') {
                $role->syncPermissions([
                    'account_manager',
                    'atm_bank_note_manager',
                    'transactions_manager',
                    'users_manager'
                ]);
            }
        }
    }
}
