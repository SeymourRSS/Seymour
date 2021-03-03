<?php

namespace App\Actions\Feeds;

use App\Utilities\Phrase;
use Illuminate\Support\Facades\Log;
use StageRightLabs\Actions\Action;

/**
 * Delete a subscription.
 *
 * Required:
 *  - Subscription
 */
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

        Log::info("Unsubscribed {$this->subscription->user->name} from '$this->subscription->title'");
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
        return [
            'subscription', // Subscription
        ];
    }
}
