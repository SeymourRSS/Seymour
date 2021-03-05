<?php

namespace App\Actions\Feeds;

use App\Feeds\Feed;
use App\Feeds\Reader;
use App\Models\Subscription;
use App\Models\User;
use App\Utilities\Phrase;
use App\Utilities\Str;
use Illuminate\Support\Facades\Log;
use StageRightLabs\Actions\Action;

/**
 * Subscribe a user to a feed.
 *
 * Required:
 *  - User
 *  - Url
 */
class SubscribeToFeed extends Action
{
    /**
     * The feed associated with the subscription.
     *
     * @var Feed|null
     */
    public $feed;

    /**
     * The newly created subscription.
     *
     * @var Subscription
     */
    public $subscription;

    /**
     * Handle the action.
     *
     * @param Action|array $input
     * @return self
     */
    public function handle($input = [])
    {
        // Fetch the feed's XML content
        $fetch = Reader::fetch($input['url']);
        if ($fetch->hasFailed()) {
            return $this->fail($fetch->message);
        }
        $this->feed = $fetch->feed;

        // Check for previously unsubscribed feeds...
        $this->restorePreviousSubscription($input['url'], $input['user']);

        // Create a new subscription if no previous subscription was found.
        if (!$this->subscription) {
            $this->createNewSubscription($input['url'], $input['user']);
        }

        // If we were not able to create a subscription we can return early.
        if (! $this->subscription) {
            return $this;
        }

        // Record articles for our new subscription
        $action = UpdateSubscriptionContent::execute([
            'subscription' => $this->subscription,
        ]);
        if ($action->completed()) {
            $this->subscription = $action->subscription;
        }

        Log::info("New Subscription for {$this->subscription->user->name}: '$this->subscription->title'");
        return $this->complete(Phrase::translate('SUBSCRIPTION_CREATED', [
            'title' => $this->subscription->title,
        ]));
    }

    /**
     * Check to see if a user has previously subscribed to a feed.
     *
     * @param string $url
     * @param User $user
     * @return void
     */
    protected function restorePreviousSubscription($url, $user)
    {
        $this->subscription = $user
            ->subscriptions()
            ->onlyTrashed()
            ->where('link_to_feed', $url)
            ->first();

        if (! $this->subscription) {
            return;
        }

        // Restore the subscription
        $this->subscription->restore();

        // Update the subscription definition with the most recent version of the feed
        $action = UpdateSubscriptionDefinition::execute([
            'subscription' => $this->subscription,
            'feed' => $this->feed,
        ]);

        if ($action->completed()) {
            $this->subscription = $action->subscription;
        }
    }

    /**
     * Fetch a feed and create a new subscription.
     *
     * @param string $url
     * @param User $user
     * @return void
     */
    public function createNewSubscription($url, $user)
    {
        // Convert the feed timestamp to UTC
        $timestamp = $this->feed->getTimestamp();
        if ($timestamp) {
            $timestamp->timezone('UTC');
        }

        // Create a new subscription
        $this->subscription = $user->subscriptions()->create([
            'identifier' => $this->feed->getIdentifier(),
            'slug' => Str::slug($this->feed->getTitle()),
            'title' => $this->feed->getTitle(),
            'subtitle' => $this->feed->getSubtitle(),
            'checksum' => $this->feed->getChecksum(),
            'link_to_feed' => $this->feed->getLinkToFeed(),
            'link_to_source' => $this->feed->getLinkToSource(),
            'license' => $this->feed->getLicense(),
            'rights' => $this->feed->getRights(),
            'feed_timestamp' => $timestamp,
            'variant' => $this->feed->getVariant(),
            'extra' => [
                'authors' => $this->feed->getAuthors()->toArray()
            ]
        ]);
    }

    /**
     * The input keys required by this action.
     *
     * @return array
     */
    public function required()
    {
        return [
            'user', // User
            'url' // string
        ];
    }
}
