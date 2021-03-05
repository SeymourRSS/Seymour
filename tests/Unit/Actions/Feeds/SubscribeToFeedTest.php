<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\SubscribeToFeed;
use App\Feeds\Author;
use App\Feeds\Reader;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use App\Models\Subscription;
use App\Models\User;
use App\Utilities\Phrase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscribeToFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_subscribe_to_a_feed()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        $author = new Author('Grace Hopper', 'grace@example.com', 'example.com');
        $fake = Simulator::make([
            'authors' => [$author],
            'identifier' => '0123456789',
            'linkToSource' => 'example.com',
            'linkToFeed' => 'example.com/feed',
            'rights' => 'copyright',
            'subtitle' => 'This is Atom 1.0',
            'title' => 'Example Feed',
            'timestamp' => $knownDate,
        ])->as(Variants::ATOM100);
        Reader::fake($fake);
        $user = User::factory()->create();

        $action = SubscribeToFeed::execute([
            'url' => $fake->getUrl(),
            'user' => $user,
        ]);

        $this->assertTrue($action->completed());
        $this->assertEquals('0123456789', $action->subscription->identifier);
        $this->assertEquals('example.com', $action->subscription->link_to_source);
        $this->assertEquals('example.com/feed', $action->subscription->link_to_feed);
        $this->assertEquals('copyright', $action->subscription->rights);
        $this->assertEquals('This is Atom 1.0', $action->subscription->subtitle);
        $this->assertEquals('Example Feed', $action->subscription->title);
        $this->assertEquals($author->toArray(), $action->subscription->getExtra('authors')[0]);
        $this->assertNotEmpty($action->subscription->checksum);
        $this->assertTrue($action->subscription->feed_timestamp->eq($knownDate));
    }

    /** @test */
    public function a_user_cannot_subscribe_to_a_feed_with_http_errors()
    {
        Http::fake(['example.com/rss' => Http::response([], 404)]);
        $user = User::factory()->create();

        $action = SubscribeToFeed::execute([
            'url' => 'example.com/rss',
            'user' => $user,
        ]);

        $this->assertTrue($action->failed());
        $this->assertEquals(Phrase::FEED_HTTP_ERROR, $action->getMessage());
        $this->assertCount(0, $user->subscriptions);
    }

    /** @test */
    public function a_user_cannot_subscribe_to_a_feed_with_invalid_formatting()
    {
        Reader::fake('example.com/feed', 'invalid.xml');
        $user = User::factory()->create();

        $action = SubscribeToFeed::execute([
            'url' => 'example.com/feed',
            'user' => $user,
        ]);

        $this->assertTrue($action->failed());
        $this->assertEquals(Phrase::ATTEMPTED_INVALID_XML, $action->getMessage());
        $this->assertCount(0, $user->subscriptions);
    }

    /** @test */
    public function a_user_can_resubscribe_to_a_previously_subscribed_feed()
    {
        $fake = Simulator::make(['linkToFeed' => 'example.com/feed'])
            ->as(Variants::ATOM100);
        Reader::fake($fake);
        $user = User::factory()->create();
        Subscription::factory()->unsubscribed()->create([
            'link_to_feed' => 'example.com/feed',
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $user->subscriptions()->onlyTrashed()->get());

        $action = SubscribeToFeed::execute([
            'url' => 'example.com/feed',
            'user' => $user,
        ]);

        $this->assertTrue($action->completed());
        $this->assertCount(1, $user->subscriptions()->get());
        $this->assertCount(1, $user->subscriptions()->withTrashed()->get());
        $this->assertCount(0, $user->subscriptions()->onlyTrashed()->get());
    }

    /** @test */
    public function it_records_timestamps_as_utc()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12, 0, 0, 'America/Los_Angeles');
        $fake = Simulator::make([
            'timestamp' => $knownDate,
        ])->as(Variants::ATOM100);
        Reader::fake($fake);
        $user = User::factory()->create();

        $action = SubscribeToFeed::execute([
            'url' => $fake->getUrl(),
            'user' => $user,
        ]);

        $this->assertTrue($action->completed());
        $this->assertTrue($action->subscription->feed_timestamp->eq($knownDate->clone()->timezone('UTC')));
    }
}
