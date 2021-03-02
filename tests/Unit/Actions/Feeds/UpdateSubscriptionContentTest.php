<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\UpdateSubscriptionContent;
use App\Feeds\Reader;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateSubscriptionContentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_records_articles()
    {
        $fake = Simulator::make([
            'linkToFeed' => 'example.com/feed',
        ])->withEntry([
            'identifier' => 'guid1',
        ])->withEntry([
            'identifier' => 'guid2',
        ])->as(Variants::ATOM100);
        Reader::fake($fake);
        $subscription = Subscription::factory()->create([
            'link_to_feed' => 'example.com/feed'
        ]);
        $this->assertCount(0, $subscription->articles);

        $action = UpdateSubscriptionContent::execute([
            'subscription' => $subscription,
        ]);

        $this->assertTrue($action->completed());
        $this->assertCount(2, $action->subscription->articles);
        $subscription->articles->each(function($article, $index) {
            $this->assertEquals('guid' . $index+1, $article->identifier);
        });
    }

    /** @test */
    public function it_handles_http_errors()
    {
        Http::fake(['example.com/feed' => Http::response([], 404)]);
        $subscription = Subscription::factory()->create([
            'link_to_feed' => 'example.com/feed'
        ]);
        $this->assertCount(0, $subscription->articles);

        $action = UpdateSubscriptionContent::execute([
            'subscription' => $subscription,
        ]);

        $this->assertTrue($action->failed());
        $this->assertCount(0, $subscription->articles);
    }

    /** @test */
    public function it_does_not_record_duplicate_articles()
    {
        $fake = Simulator::make([
                'linkToFeed' => 'example.com/feed',
            ])
            ->withEntry(['identifier' => 'guid1'])
            ->withEntry(['identifier' => 'guid2'])
            ->as(Variants::ATOM100);
        Http::fake(['example.com/feed' => Http::sequence()
            ->push($fake->toString(), 200)
            ->push($fake->toString(), 200)
        ]);
        $subscription = Subscription::factory()->create([
            'link_to_feed' => 'example.com/feed'
        ]);
        UpdateSubscriptionContent::execute(['subscription' => $subscription]);
        $this->assertCount(2, $subscription->articles);

        $action = UpdateSubscriptionContent::execute([
            'subscription' => $subscription,
        ]);

        $this->assertTrue($action->completed());
        $this->assertCount(2, $action->subscription->articles);
    }

    /** @test */
    public function it_records_newly_discovered_articles()
    {
        $firstFake = Simulator::make([
                'linkToFeed' => 'example.com/feed',
            ])
            ->withEntry(['identifier' => 'guid1'])
            ->withEntry(['identifier' => 'guid2'])
            ->as(Variants::ATOM100);
        $secondFake = Simulator::make([
                'linkToFeed' => 'example.com/feed',
            ])
            ->withEntry(['identifier' => 'guid2'])
            ->withEntry(['identifier' => 'guid3'])
            ->as(Variants::ATOM100);
        Http::fake(['example.com/feed' => Http::sequence()
            ->push($firstFake->toString(), 200)
            ->push($secondFake->toString(), 200)
        ]);
        $subscription = Subscription::factory()->create([
            'link_to_feed' => 'example.com/feed'
        ]);
        UpdateSubscriptionContent::execute(['subscription' => $subscription]);
        $this->assertCount(2, $subscription->articles);

        $action = UpdateSubscriptionContent::execute([
            'subscription' => $subscription,
        ]);

        $this->assertTrue($action->completed());
        $this->assertCount(3, $action->subscription->articles);
        $subscription->articles->each(function ($article, $index) {
            $this->assertEquals('guid' . $index + 1, $article->identifier);
        });
    }

    /** @test */
    public function it_requires_a_subscription()
    {
        $subscription = Subscription::factory()->create([
            'link_to_feed' => 'example.com/feed'
        ]);
        $this->assertCount(0, $subscription->articles);

        $action = UpdateSubscriptionContent::execute();

        $this->assertFalse($action->completed());
        $this->assertCount(0, $subscription->refresh()->articles);
    }
}
