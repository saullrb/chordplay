<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@cp.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
            'role_id' => Role::ADMIN,
        ]);
    }
}
