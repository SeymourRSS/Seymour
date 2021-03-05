<?php

namespace Database\Seeders;


use Database\Seeders\ArticleSeeder;
use Database\Seeders\SubscriptionSeeder;
use Database\Seeders\UserSeeder;
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
        $this->call([
            UserSeeder::class,
            SubscriptionSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
