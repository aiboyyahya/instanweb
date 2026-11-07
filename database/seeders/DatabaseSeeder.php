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
        // User::factory(10)->create();

        User::updateOrCreate([
            'email' => 'bimboahmad78@gmail.com',
        ], [
            'name' => 'Jaka',
            'role' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        $this->call([
            DummyDataSeeder::class,
        ]);
    }
}
