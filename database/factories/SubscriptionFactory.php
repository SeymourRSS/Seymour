<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use App\Utilities\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence();

        return [
            'checksum' => '123456789',
            'feed_timestamp' => now(),
            'identifier' => $this->faker->uuid(),
            'link_to_source' => $this->faker->url(),
            'link_to_feed' => $this->faker->url(),
            'rights' => 'Copyright Information',
            'slug' => Str::slug($title),
            'subtitle' => $this->faker->sentence(),
            'title' => $title,
            'user_id' => User::factory()
        ];
    }

    /**
     * Indicate that a subscription has been removed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unsubscribed()
    {
        return $this->state(function(array $attributes) {
            return [
                'deleted_at' => now()
            ];
        });
    }
}
