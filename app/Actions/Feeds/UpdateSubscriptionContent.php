<?php

namespace App\Actions\Feeds;

use App\Actions\Feeds\RecordArticle;
use App\Feeds\Feed;
use App\Feeds\Reader;
use App\Models\Subscription;
use StageRightLabs\Actions\Action;

/**
 * Check a subscription's feed for new content and capture it.
 *
 * Required:
 *  - Subscription
 */
class UpdateSubscriptionContent extends Action
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
     * @var Collection
     */
    public $results;

    /**
     * Handle the action.
     *
     * @param Action|array $input
     * @return self
     */
    public function handle($input = [])
    {
        $this->subscription = $input['subscription'];

        // Fetch the latest version of the subscription's feed
        $fetch = Reader::fetch($this->subscription->link_to_feed);

        // Ensure we were able to fetch the feed entries
        if ($fetch->hasFailed()) {
            return $this->fail($fetch->message);
        }

        // Ensure we have entries to work with
        $this->feed = $fetch->feed;
        $entries = $this->feed->getEntries();
        if ($entries->isEmpty()) {
            return $this->complete();
        }

        // Update the subscription definition
        $action = UpdateSubscriptionDefinition::execute([
            'feed' => $this->feed,
            'subscription' => $this->subscription,
        ]);

        if ($action->completed()) {
            $this->subscription = $action->subscription;
        }

        // Fetch the identifiers of the entries that have already been recorded.
        $knownIdentifiers = $this->subscription->articles()
            ->withTrashed()
            ->pluck('identifier');

        // Loop through the entries and create new articles when appropriate
        $this->results = $entries->map(function ($entry) use ($knownIdentifiers) {

            // Has this entry been recorded already?
            if ($knownIdentifiers->contains($entry->getIdentifier())) {
                return null;
            }

            // Create a new article from this entry.
            return RecordArticle::execute([
                'entry' => $entry,
                'subscription' => $this->subscription,
            ]);
        })->filter();

        // Update the subscriptions articles relationship
        $this->subscription->load('articles');

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
            'subscription', // Subscription
        ];
    }
}
