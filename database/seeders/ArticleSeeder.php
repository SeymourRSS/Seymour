<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed the articles for each subscription
        Subscription::get()
            ->map(function ($subscription) {
                return collect(range(1, rand(5, 10)))
                    ->each(function () use ($subscription) {
                        Article::factory()->create([
                            'subscription_uuid' => $subscription->uuid,
                        ]);
                    });
            })
            ->flatten();
    }
}
