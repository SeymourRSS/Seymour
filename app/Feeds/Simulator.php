<?php

namespace App\Feeds;

use App\Feeds\Rss90\Rss90;
use App\Feeds\Variants;
use App\Utilities\Arr;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Carbon;

class Simulator
{
    /**
     * @var array
     */
    public $authors = [];

    /**
     * @var array
     */
    public $entries = [];

    /**
     * @var \Faker\Generator
     */
    public $faker;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $linkToSource;

    /**
     * @var string
     */
    public $linkToFeed;

    /**
     * @var string
     */
    public $rights;

    /**
     * @var string
     */
    public $subtitle;

    /**
     * @var Carbon
     */
    public $timestamp;

    /**
     * @var string
     */
    public $title;

    /**
     * Create a new Simulator
     *
     * @return void
     */
    public function __construct()
    {
        $locale = $locale ?? config('app.faker_locale', Factory::DEFAULT_LOCALE);
        $this->faker = Factory::create($locale);
    }

    /**
     * Initialize a new fake feed
     *
     * @param array $attributes
     * @return Simulator
     */
    public static function make($attributes = []): Simulator
    {
        $feed = new Simulator;

        $feed->authors = Arr::get($attributes, 'authors', []);
        $feed->identifier = Arr::get($attributes, 'identifier', $feed->faker->uuid());
        $feed->linkToSource = Arr::get($attributes, 'linkToSource', $feed->faker->url);
        $feed->linkToFeed = Arr::get($attributes, 'linkToFeed', $feed->faker->url);
        $feed->rights = Arr::get($attributes, 'rights', 'Copyright Information');
        $feed->subtitle = Arr::get($attributes, 'subtitle', $feed->faker->sentence());
        $feed->title = Arr::get($attributes, 'title', $feed->faker->sentence());
        $feed->timestamp = Arr::get($attributes, 'timestamp', now());

        return $feed;
    }

    /**
     * Add an entry to this fake feed
     *
     * @param array $attributes
     * @return self
     */
    public function withEntry($attributes = []): Simulator
    {
        $entry = [];

        $entry['authors'] = Arr::get($attributes, 'authors', []);
        $entry['content'] = Arr::get($attributes, 'content', $this->faker->randomHtml());
        $entry['identifier'] = Arr::Get($attributes, 'identifier', $this->faker->uuid());
        $entry['linkToSource'] = Arr::get($attributes, 'linkToSource', $this->faker->url());
        $entry['summary'] = Arr::get($attributes, 'summary', $this->faker->sentence());
        $entry['title'] = Arr::get($attributes, 'title', $this->faker->sentence());
        $entry['timestamp'] = Arr::get($attributes, 'timestamp', now());

        $this->entries[] = $entry;

        return $this;
    }

    /**
     * Determine which feed type to simulate.
     *
     * @param string $type
     * @return FakeFeed
     */
    public function as($type)
    {
        return Variants::fake($type, $this);
    }
}
