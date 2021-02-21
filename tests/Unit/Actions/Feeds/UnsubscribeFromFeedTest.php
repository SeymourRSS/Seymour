<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\SubscribeToFeed;
use App\Actions\Feeds\UnsubscribeFromFeed;
use App\Feeds\Reader;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use App\Models\Subscription;
use App\Models\User;
use App\Utilities\Phrase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsubscribeFromFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_unsubscribe_from_a_feed()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        $action = UnsubscribeFromFeed::execute([
            'subscription' => $subscription
        ]);

        $this->assertTrue($action->completed());
        $this->assertEquals(Phrase::SUBSCRIPTION_REMOVED, $action->getMessage());
        $this->assertCount(0, $user->subscriptions);
    }
}
