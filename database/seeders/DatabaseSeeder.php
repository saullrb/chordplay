<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artist::factory(30)->has(Song::factory(20))->create();
    }
}
