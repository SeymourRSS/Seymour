<?php

namespace App\Actions\Feeds;

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

        // Dispatch job for storing articles...

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

        if ($this->subscription) {
            $this->subscription->restore();
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
        $fetch = Reader::fetch($url);

        if ($fetch->hasFailed()) {
            return $this->fail($fetch->message);
        }

        $timestamp = $fetch->feed->getTimestamp();
        if ($timestamp) {
            $timestamp->timezone('UTC');
        }

        $this->subscription = $user->subscriptions()->create([
            'identifier' => $fetch->feed->getIdentifier(),
            'slug' => Str::slug($fetch->feed->getTitle()),
            'title' => $fetch->feed->getTitle(),
            'subtitle' => $fetch->feed->getSubtitle(),
            'checksum' => $fetch->feed->getChecksum(),
            'link_to_feed' => $fetch->feed->getLinkToFeed(),
            'link_to_source' => $fetch->feed->getLinkToSource(),
            'license' => $fetch->feed->getLicense(),
            'rights' => $fetch->feed->getRights(),
            'feed_timestamp' => $timestamp,
            'variant' => $fetch->feed->getVariant(),
            'extra' => [
                'authors' => $fetch->feed->getAuthors()->toArray()
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
