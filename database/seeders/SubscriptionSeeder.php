<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed some subscriptions for each user
        User::get()
            ->map(function ($user) {
                return collect(range(1, rand(2, 5)))
                    ->map(function () use ($user) {
                        return Subscription::factory()->create([
                            'user_id' => $user->id,
                        ]);
                    });
            })
            ->flatten();
    }
}
