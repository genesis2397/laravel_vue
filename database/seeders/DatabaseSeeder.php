<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'photos/ZB35i5FAeSNJP4LfOskTYkRkHvAm2qt4HAhheUXy.jpg'
        ]);
    }
}
