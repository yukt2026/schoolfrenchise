<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminAccounts = [
            [
                'name' => 'TECH ERA Super Admin',
                'email' => 'admin@techeracomputer.in',
                'password' => 'Admin@12345',
                'mobile' => '9162384660',
            ],
            [
                'name' => 'TECH ERA Computer Admin',
                'email' => 'admin@drishticomputer.com',
                'password' => 'Admin@12345',
                'mobile' => '9942434239',
            ],
        ];

        $superRole = Role::where('name', 'super_admin')->first();

        foreach ($adminAccounts as $acc) {
            $superAdmin = User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'name' => $acc['name'],
                    'mobile' => $acc['mobile'],
                    'password' => Hash::make($acc['password']),
                    'role' => 'super_admin',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            if ($superRole) {
                $superAdmin->roles()->syncWithoutDetaching([$superRole->id]);
            }
        }

        // Record initialization in audit logs
        AuditLog::create([
            'user_id' => $superAdmin->id,
            'action' => 'super_admin_provisioned',
            'auditable_type' => User::class,
            'auditable_id' => $superAdmin->id,
            'new_values' => [
                'email' => $superAdmin->email,
                'role' => 'super_admin',
                'provisioned_via' => 'SuperAdminSeeder',
                'timestamp' => now()->toIso8601String(),
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Artisan CLI / Seeder',
        ]);
    }
}
