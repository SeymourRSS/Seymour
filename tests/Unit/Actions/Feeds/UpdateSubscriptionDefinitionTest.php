<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\UpdateSubscriptionDefinition;
use App\Feeds\Author;
use App\Feeds\Reader;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpdateSubscriptionDefinitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_a_subscriptions_definition()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        $author = new Author('Grace Hopper', 'grace@example.com', 'example.com');
        Carbon::setTestNow($knownDate);
        $subscription = Subscription::factory()->create([
            'checksum' => 'original_checksum',
            'link_to_feed' => 'example.com/feed',
        ]);
        $fake = Simulator::make([
            'authors' => [$author],
            'identifier' => '0123456789',
            'linkToSource' => 'example.com',
            'linkToFeed' => 'example.com/feed',
            'rights' => 'copyright',
            'subtitle' => 'This is Atom 1.0',
            'title' => 'New Feed Title',
            'timestamp' => $knownDate,
        ])->as(Variants::ATOM100);
        Reader::fake($fake);
        $fetch = Reader::fetch('example.com/feed');

        $action = UpdateSubscriptionDefinition::execute([
            'feed' => $fetch->feed,
            'subscription' => $subscription,
        ]);

        $this->assertTrue($action->completed());
        $this->assertEquals('0123456789', $action->subscription->identifier);
        $this->assertNotEquals('original_checksum', $subscription->checksum);
        $this->assertEquals('example.com', $action->subscription->link_to_source);
        $this->assertEquals('example.com/feed', $action->subscription->link_to_feed);
        $this->assertEquals('copyright', $action->subscription->rights);
        $this->assertEquals('This is Atom 1.0', $action->subscription->subtitle);
        $this->assertEquals('New Feed Title', $action->subscription->title);
        $this->assertEquals($author->toArray(), $action->subscription->getExtra('authors')[0]);
        $this->assertNotEmpty($action->subscription->checksum);
        $this->assertTrue($action->subscription->feed_timestamp->eq($knownDate));
    }

    /** @test */
    public function it_requires_a_subscription()
    {
        $fake = Simulator::make()->as(Variants::ATOM100);
        Reader::fake($fake);
        $fetch = Reader::fetch('example.com/feed');

        $action = UpdateSubscriptionDefinition::execute([
            'feed' => $fetch->feed,
        ]);

        $this->assertFalse($action->completed());
    }

    /** @test */
    public function it_requires_a_feed()
    {
        $subscription = Subscription::factory()->create();

        $action = UpdateSubscriptionDefinition::execute([
            'subscription' => $subscription,
        ]);

        $this->assertFalse($action->completed());
    }
}
