<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artist::factory()
            ->count(10)
            ->create();

        $this->call(ChordSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(AdminUserSeeder::class);

        User::create([
            'name' => 'Staff',
            'email' => 'staff@cp.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
            'role_id' => Role::STAFF,
        ]);
        User::create([
            'name' => 'Normal User',
            'email' => 'user@cp.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
            'role_id' => Role::USER,
        ]);
    }
}
