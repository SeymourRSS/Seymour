<?php

namespace App\Actions\Feeds;

use App\Utilities\Phrase;
use StageRightLabs\Actions\Action;

class UnsubscribeFromFeed extends Action
{
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
        $this->subscription = $input['subscription'];

        $this->subscription->delete();

        return $this->complete(Phrase::translate('SUBSCRIPTION_REMOVED', [
            'title' => $this->subscription->title,
        ]));
    }

    /**
     * The input keys required by this action.
     *
     * @return array
     */
    public function required()
    {
        return ['subscription'];
    }
}
