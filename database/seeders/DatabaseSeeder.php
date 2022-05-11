<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'is_admin' => true
        ]);

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@mail.com',
        ]);
    }
}
