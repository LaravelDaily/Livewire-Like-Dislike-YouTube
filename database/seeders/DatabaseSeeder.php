<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'admin@admin.com',
        ]);

        $this->call([
            UserSeeder::class,
        ]);

        Post::factory(50)->create();
    }
}
