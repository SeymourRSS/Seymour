<?php

namespace App\Actions\Feeds;

use App\Feeds\Feed;
use App\Models\Subscription;
use App\Utilities\Str;
use StageRightLabs\Actions\Action;

/**
 * Update a subscription's meta-data with values fetched from a feed.
 *
 * Required:
 *  - Feed
 *  - Subscription
 */
class UpdateSubscriptionDefinition extends Action
{
    /**
     * @var Feed
     */
    public $feed;

    /**
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
        $this->feed = $input['feed'];
        $this->subscription = $input['subscription'];

        // Update the subscription model.
        $this->subscription->update([
            'identifier' => $this->feed->getIdentifier(),
            'slug' => Str::slug($this->feed->getTitle()),
            'title' => $this->feed->getTitle(),
            'subtitle' => $this->feed->getSubtitle(),
            'checksum' => $this->feed->getChecksum(),
            'link_to_feed' => $this->feed->getLinkToFeed(),
            'link_to_source' => $this->feed->getLinkToSource(),
            'license' => $this->feed->getLicense(),
            'rights' => $this->feed->getRights(),
            'feed_timestamp' => $this->feed->getTimestamp(),
            'variant' => $this->feed->getVariant(),
        ]);

        // Extras
        $this->subscription->setExtra('authors', $this->feed->getAuthors()->toArray());

        return $this->complete();
    }

    /**
     * The input keys required by this action.
     *
     * @return array
     */
    public function required()
    {
        return [
            'feed', // Feed
            'subscription', // Subscription
        ];
    }
}
