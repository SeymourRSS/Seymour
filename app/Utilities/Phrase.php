<?php

namespace App\Utilities;

/**
 * Here we define all of the language strings that are used by the
 * application business logic. Translation will happen in the
 * view templates, using these strings as translation keys.
 */
class Phrase
{
    // Feeds
    const FEED_HTTP_ERROR = 'There was a problem fetching the content of this feed.';
    const ATTEMPTED_INVALID_XML = 'Attempted to parse invalid XML.';
    const UNKNOWN_FEED_VARIANT = 'Unknown feed variant.';

    // Subscriptions
    const SUBSCRIPTION_CREATED = "You have been subscribed to ':title'";
    const SUBSCRIPTION_REMOVED = "You have been unsubscribed from ':title'";
}
