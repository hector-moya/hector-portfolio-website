<?php

namespace Database\Seeders;

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
        // Create admin user
        User::factory()->admin()->withoutTwoFactor()->create([
            'name' => config('admin.name'),
            'email' => config('admin.email'),
            'password' => bcrypt(config('admin.password')),
            'role' => 'admin',
        ]);

        // Create editor user
        User::factory()->editor()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ]);

        // Create viewer user
        User::factory()->viewer()->create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
        ]);

        $this->call(BlogContentSeeder::class);
    }
}
