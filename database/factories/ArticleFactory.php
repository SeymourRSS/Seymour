<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Subscription;
use App\Utilities\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence();

        return [
            'content' => $this->faker->randomHtml(),
            'entry_timestamp' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'has_been_read' => false,
            'identifier' => Str::random(16),
            'link_to_source' => $this->faker->url(),
            'slug' => Str::slug($title),
            'subscription_uuid' => Subscription::factory(),
            'summary' => $this->faker->sentence(),
            'title' => $title,
        ];
    }

    /**
     * Indicate that an article has been read.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'has_been_read' => true,
            ];
        });
    }

    /**
     * Indicate that an article has been removed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now(),
            ];
        });
    }
}
