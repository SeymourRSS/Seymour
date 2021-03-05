<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create five imaginary user accounts
        collect(range(1, 5))->map(function ($number) {
            return User::factory()->create([
                'email' => "user{$number}@example.com",
            ]);
        });
    }
}
